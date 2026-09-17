<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBahanBakuRequest;
use App\Models\ActivityLog;
use App\Models\BahanBaku;
use App\Models\BahanBakuHistori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::query();

        if ($search = $request->input('search')) {
            $query->where('nama_bahan', 'like', "%{$search}%");
        }

        if ($request->boolean('stok_menipis')) {
            $query->whereColumn('stok_total', '<=', 'stok_minimum');
        }

        $bahanBaku = $query->orderBy('nama_bahan', 'asc')->paginate(10)->withQueryString();

        // Seluruh daftar bahan untuk pilihan restock (utamakan yang stoknya menipis di atas)
        $allBahanList = BahanBaku::orderByRaw('(stok_total <= stok_minimum) DESC, nama_bahan ASC')->get();

        // Agregasi SQL untuk kartu ringkasan
        $totalItem = BahanBaku::count();
        $totalNilaiInventaris = BahanBaku::selectRaw('SUM(stok_total * harga_per_satuan) as total_nilai')->value('total_nilai') ?? 0;
        $totalStokMenipis = BahanBaku::whereColumn('stok_total', '<=', 'stok_minimum')->count();

        return view('bahan-baku.index', compact('bahanBaku', 'allBahanList', 'totalItem', 'totalNilaiInventaris', 'totalStokMenipis'));
    }

    public function store(StoreBahanBakuRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $mode = $request->input('mode', 'existing');
            $jumlahBaru = (float) $validated['jumlah'];
            $hargaBeliBaru = (float) $validated['harga_beli'];
            $stokMinimum = isset($validated['stok_minimum']) && $validated['stok_minimum'] !== '' ? (float)$validated['stok_minimum'] : null;
            $tanggal = $validated['tanggal'] ?? now()->toDateString();
            $keterangan = $validated['keterangan'] ?? 'Restock Stok Bahan';

            $isNew = false;
            if ($mode === 'existing' && !empty($validated['bahan_baku_id'])) {
                $bahan = BahanBaku::findOrFail($validated['bahan_baku_id']);

                // Logika Akumulasi Stok & Weighted Average Cost (WAC)
                $stokLama = (float) $bahan->stok_total;
                $totalBeliLama = (float) $bahan->total_harga_beli;

                $stokBaru = $stokLama + $jumlahBaru;
                $totalBeliBaru = $totalBeliLama + $hargaBeliBaru;
                $hargaPerSatuanBaru = $stokBaru > 0 ? ($totalBeliBaru / $stokBaru) : 0;

                $updateData = [
                    'stok_total' => $stokBaru,
                    'total_harga_beli' => $totalBeliBaru,
                    'harga_per_satuan' => round($hargaPerSatuanBaru, 4),
                ];
                if ($stokMinimum !== null) {
                    $updateData['stok_minimum'] = $stokMinimum;
                }
                $bahan->update($updateData);
            } else {
                $isNew = true;
                $namaBahan = trim($validated['nama_bahan']);
                $satuan = $validated['satuan'];
                $hargaPerSatuan = $jumlahBaru > 0 ? ($hargaBeliBaru / $jumlahBaru) : 0;

                // Cek apakah bahan dengan nama & satuan yang sama sudah ada di database
                $existing = BahanBaku::whereRaw('LOWER(nama_bahan) = ?', [strtolower($namaBahan)])
                                     ->where('satuan', $satuan)
                                     ->first();

                if ($existing) {
                    $bahan = $existing;
                    $isNew = false;
                    $stokLama = (float) $bahan->stok_total;
                    $totalBeliLama = (float) $bahan->total_harga_beli;
                    $stokBaru = $stokLama + $jumlahBaru;
                    $totalBeliBaru = $totalBeliLama + $hargaBeliBaru;
                    $hargaPerSatuanBaru = $stokBaru > 0 ? ($totalBeliBaru / $stokBaru) : 0;
                    $bahan->update([
                        'stok_total' => $stokBaru,
                        'total_harga_beli' => $totalBeliBaru,
                        'harga_per_satuan' => round($hargaPerSatuanBaru, 4),
                        'stok_minimum' => $stokMinimum ?? $bahan->stok_minimum,
                    ]);
                } else {
                    $bahan = BahanBaku::create([
                        'nama_bahan' => $namaBahan,
                        'satuan' => $satuan,
                        'stok_total' => $jumlahBaru,
                        'total_harga_beli' => $hargaBeliBaru,
                        'harga_per_satuan' => round($hargaPerSatuan, 4),
                        'stok_minimum' => $stokMinimum ?? 500.00,
                    ]);
                }
            }

            // Simpan audit trail di histori
            BahanBakuHistori::create([
                'bahan_baku_id' => $bahan->id,
                'jumlah_ditambahkan' => $jumlahBaru,
                'harga_beli' => $hargaBeliBaru,
                'tanggal' => $tanggal,
                'keterangan' => $keterangan,
            ]);

            // Re-kalkulasi HPP seluruh menu yang memakai bahan baku ini
            $menus = Menu::whereHas('resep', function ($q) use ($bahan) {
                $q->where('bahan_baku_id', $bahan->id);
            })->get();

            foreach ($menus as $menu) {
                $menu->calculateCost();
            }

            // Log aktivitas
            ActivityLog::log(
                $isNew ? 'TAMBAH_BAHAN_BAKU_BARU' : 'RESTOCK_BAHAN_BAKU',
                ($isNew ? 'Membuat bahan baku baru: ' : 'Restock bahan baku: ') . $bahan->nama_bahan . " (+{$jumlahBaru} {$bahan->satuan})",
                [
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => $jumlahBaru,
                    'harga_beli' => $hargaBeliBaru,
                    'harga_per_satuan' => $bahan->harga_per_satuan,
                ]
            );
        });

        return redirect()->route('bahan-baku.index')->with('success', 'Data bahan baku dan stok berhasil diperbarui.');
    }

    public function histori(BahanBaku $bahanBaku)
    {
        $histori = $bahanBaku->histori()->orderBy('tanggal', 'desc')->paginate(15);
        return view('bahan-baku.histori', compact('bahanBaku', 'histori'));
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        // Cek apakah bahan baku digunakan di menu
        $usedCount = $bahanBaku->resep()->count();
        if ($usedCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus {$bahanBaku->nama_bahan} karena sedang digunakan di {$usedCount} resep menu.");
        }

        $nama = $bahanBaku->nama_bahan;
        $bahanBaku->delete();

        ActivityLog::log('HAPUS_BAHAN_BAKU', "Menghapus bahan baku: {$nama}");

        return redirect()->route('bahan-baku.index')->with('success', "Bahan baku {$nama} berhasil dihapus.");
    }
}
