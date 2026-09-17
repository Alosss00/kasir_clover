<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BahanBaku;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('resep.bahanBaku')->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        if ($kategori = $request->input('kategori')) {
            if ($kategori !== 'all') {
                $query->where('kategori', $kategori);
            }
        }

        $menuList = $query->orderBy('nama_menu', 'asc')->get();
        $categories = Menu::where('is_active', true)->select('kategori')->distinct()->pluck('kategori');

        // Transaksi terakhir untuk kasir ini
        $recentTransactions = Transaksi::where('kasir_id', Auth::id())
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('kasir.index', compact('menuList', 'categories', 'recentTransactions'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menu,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'metode_pembayaran' => ['required', 'in:cash,debit_qris'],
            'jumlah_bayar' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $transaksi = DB::transaction(function () use ($validated) {
            $totalHarga = 0;
            $itemsData = [];

            // 1. Ambil data menu dan hitung total harga
            foreach ($validated['items'] as $itemInput) {
                $menu = Menu::with('resep')->findOrFail($itemInput['menu_id']);
                $qty = (int) $itemInput['qty'];
                $subtotal = (float) $menu->harga_jual * $qty;
                $totalHarga += $subtotal;

                $itemsData[] = [
                    'menu' => $menu,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            // Validasi uang bayar jika tunai
            $metode = $validated['metode_pembayaran'];
            $jumlahBayar = null;
            $kembalian = null;

            if ($metode === 'cash') {
                $jumlahBayar = (float) ($validated['jumlah_bayar'] ?? 0);
                if ($jumlahBayar < $totalHarga) {
                    abort(422, 'Nominal uang diterima kurang dari total belanja.');
                }
                $kembalian = $jumlahBayar - $totalHarga;
            }

            // 2. Simpan Header Transaksi
            $kodeTransaksi = Transaksi::generateKodeTransaksi();
            $trx = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'kasir_id' => Auth::id(),
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $metode,
                'jumlah_bayar' => $jumlahBayar,
                'kembalian' => $kembalian,
                'tanggal_transaksi' => now(),
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // 3. Simpan Transaksi Detail Snapshot & Potong Stok Bahan
            foreach ($itemsData as $row) {
                $menu = $row['menu'];
                $qty = $row['qty'];

                TransaksiDetail::create([
                    'transaksi_id' => $trx->id,
                    'menu_id' => $menu->id,
                    'nama_menu_snapshot' => $menu->nama_menu,
                    'harga_satuan_snapshot' => $menu->harga_jual,
                    'cost_per_cup_snapshot' => $menu->cost_per_cup,
                    'qty' => $qty,
                    'subtotal' => $row['subtotal'],
                ]);

                // Potong stok bahan baku berdasarkan resep
                if ($menu->resep) {
                    foreach ($menu->resep as $resep) {
                        $totalKurang = (float) $resep->jumlah_pemakaian * $qty;
                        $bahan = BahanBaku::find($resep->bahan_baku_id);
                        if ($bahan) {
                            $stokBaru = max(0, (float)$bahan->stok_total - $totalKurang);
                            $bahan->update(['stok_total' => $stokBaru]);
                        }
                    }
                }
            }

            // 4. Audit Log
            ActivityLog::log(
                'TRANSAKSI_POS',
                "Transaksi kasir {$trx->kode_transaksi} sukses diproses senilai Rp " . number_format($totalHarga, 0, ',', '.'),
                ['transaksi_id' => $trx->id, 'total' => $totalHarga, 'metode' => $metode]
            );

            return $trx;
        });

        if ($request->wantsJson() || $request->ajax()) {
            $transaksi->load(['details', 'kasir']);
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'transaksi' => $transaksi,
            ]);
        }

        return redirect()->route('kasir.index')
            ->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil diproses!")
            ->with('receipt_id', $transaksi->id);
    }

    public function cetakStruk(Transaksi $transaksi)
    {
        $transaksi->load(['details', 'kasir']);
        return view('kasir.struk', compact('transaksi'));
    }
}
