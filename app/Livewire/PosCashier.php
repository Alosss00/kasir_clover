<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\BahanBaku;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PosCashier extends Component
{
    public $search = '';
    public $selectedCategory = 'all';

    // In-memory cart: [ ['menu_id' => 1, 'nama' => '...', 'harga' => 25000, 'cost_per_cup' => 11540, 'qty' => 2, 'subtotal' => 50000] ]
    public $cart = [];

    // Checkout modal & payment state
    public $showCheckoutModal = false;
    public $metodePembayaran = 'cash'; // 'cash' or 'debit_qris'
    public $jumlahBayar = 0;
    public $kembalian = 0;
    public $catatan = '';

    // Receipt print state
    public $lastTransaksi = null;
    public $showReceiptModal = false;

    public function addToCart($menuId)
    {
        $menu = Menu::where('id', $menuId)->where('is_active', true)->first();
        if (!$menu) return;

        // Cek apakah item sudah ada di keranjang
        $existingIndex = null;
        foreach ($this->cart as $index => $item) {
            if ($item['menu_id'] == $menuId) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $this->cart[$existingIndex]['qty']++;
            $this->cart[$existingIndex]['subtotal'] = $this->cart[$existingIndex]['qty'] * $this->cart[$existingIndex]['harga'];
        } else {
            $this->cart[] = [
                'menu_id' => $menu->id,
                'nama' => $menu->nama_menu,
                'kategori' => $menu->kategori,
                'harga' => (float) $menu->harga_jual,
                'cost_per_cup' => (float) $menu->cost_per_cup,
                'qty' => 1,
                'subtotal' => (float) $menu->harga_jual,
            ];
        }
    }

    public function updateQty($index, $change)
    {
        if (isset($this->cart[$index])) {
            $newQty = $this->cart[$index]['qty'] + $change;
            if ($newQty > 0) {
                $this->cart[$index]['qty'] = $newQty;
                $this->cart[$index]['subtotal'] = $newQty * $this->cart[$index]['harga'];
            } else {
                $this->removeFromCart($index);
            }
        }
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            array_splice($this->cart, $index, 1);
        }
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    public function getTotalHargaProperty(): float
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + $item['subtotal'];
        }, 0);
    }

    public function getTotalItemsProperty(): int
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + $item['qty'];
        }, 0);
    }

    public function openCheckout()
    {
        if (empty($this->cart)) return;

        $this->metodePembayaran = 'cash';
        $this->jumlahBayar = $this->totalHarga;
        $this->calculateKembalian();
        $this->showCheckoutModal = true;
    }

    public function updatedJumlahBayar()
    {
        $this->calculateKembalian();
    }

    public function calculateKembalian()
    {
        if ($this->metodePembayaran === 'cash') {
            $bayar = (float) $this->jumlahBayar;
            $total = $this->totalHarga;
            $this->kembalian = max(0, $bayar - $total);
        } else {
            $this->kembalian = 0;
        }
    }

    public function setQuickCash($amount)
    {
        $this->jumlahBayar = $amount;
        $this->calculateKembalian();
    }

    public function setExactCash()
    {
        $this->jumlahBayar = $this->totalHarga;
        $this->calculateKembalian();
    }

    public function processCheckout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja masih kosong.');
            return;
        }

        $total = $this->totalHarga;

        // Validasi Cash
        if ($this->metodePembayaran === 'cash') {
            if ((float)$this->jumlahBayar < $total) {
                session()->flash('error', 'Nominal uang dibayar kurang dari total transaksi.');
                return;
            }
        }

        // Eksekusi Single DB Transaction
        $transaksi = DB::transaction(function () use ($total) {
            $kodeTrx = Transaksi::generateKodeTransaksi();
            $bayar = $this->metodePembayaran === 'cash' ? (float)$this->jumlahBayar : null;
            $kembali = $this->metodePembayaran === 'cash' ? max(0, (float)$this->jumlahBayar - $total) : null;

            // 1. Simpan Header Transaksi
            $trx = Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'kasir_id' => Auth::id(),
                'total_harga' => $total,
                'metode_pembayaran' => $this->metodePembayaran,
                'jumlah_bayar' => $bayar,
                'kembalian' => $kembali,
                'tanggal_transaksi' => now(),
                'catatan' => $this->catatan ?: null,
            ]);

            // 2. Simpan Transaksi Detail dengan Snapshot & Potong Stok Bahan
            foreach ($this->cart as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'menu_id' => $item['menu_id'],
                    'nama_menu_snapshot' => $item['nama'],
                    'harga_satuan_snapshot' => $item['harga'],
                    'cost_per_cup_snapshot' => $item['cost_per_cup'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Potong stok bahan baku sesuai resep menu
                $menu = Menu::with('resep')->find($item['menu_id']);
                if ($menu && $menu->resep) {
                    foreach ($menu->resep as $resep) {
                        $totalPengurangan = $resep->jumlah_pemakaian * $item['qty'];
                        $bahan = BahanBaku::find($resep->bahan_baku_id);
                        if ($bahan) {
                            $stokTersisa = max(0, (float)$bahan->stok_total - $totalPengurangan);
                            $bahan->update(['stok_total' => $stokTersisa]);
                        }
                    }
                }
            }

            // 3. Log Aktivitas
            ActivityLog::log(
                'TRANSAKSI_POS',
                "Transaksi {$trx->kode_transaksi} berhasil diproses (Total: Rp " . number_format($total, 0, ',', '.') . " - {$this->metodePembayaran})",
                ['transaksi_id' => $trx->id, 'total' => $total, 'metode' => $this->metodePembayaran]
            );

            return $trx;
        });

        // Muat relasi transaksi untuk cetak struk
        $this->lastTransaksi = Transaksi::with(['details', 'kasir'])->find($transaksi->id);

        // Reset state keranjang & tutup modal checkout
        $this->cart = [];
        $this->showCheckoutModal = false;
        $this->showReceiptModal = true;
    }

    public function closeReceipt()
    {
        $this->showReceiptModal = false;
        $this->lastTransaksi = null;
    }

    public function render()
    {
        $query = Menu::where('is_active', true);

        if ($this->search) {
            $query->where('nama_menu', 'like', "%{$this->search}%");
        }

        if ($this->selectedCategory !== 'all') {
            $query->where('kategori', $this->selectedCategory);
        }

        $menuList = $query->orderBy('nama_menu', 'asc')->get();
        $categories = Menu::where('is_active', true)->select('kategori')->distinct()->pluck('kategori');

        return view('livewire.pos-cashier', [
            'menuList' => $menuList,
            'categories' => $categories,
        ]);
    }
}
