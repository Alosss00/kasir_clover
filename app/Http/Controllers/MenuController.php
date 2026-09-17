<?php

namespace App\Http\Controllers;

use App\Exports\MenuCostingExport;
use App\Http\Requests\StoreMenuRequest;
use App\Models\ActivityLog;
use App\Models\BahanBaku;
use App\Models\Menu;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['resep.bahanBaku']);

        if ($search = $request->input('search')) {
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        if ($kategori = $request->input('kategori')) {
            $query->where('kategori', $kategori);
        }

        $menus = $query->orderBy('nama_menu', 'asc')->paginate(12)->withQueryString();

        // Kategori list untuk filter
        $kategoriList = Menu::select('kategori')->distinct()->pluck('kategori');

        // Ringkasan Agregasi
        $totalMenu = Menu::count();
        $totalAktif = Menu::where('is_active', true)->count();
        $avgMargin = Menu::where('is_active', true)->where('harga_jual', '>', 0)
            ->selectRaw('AVG((keuntungan_per_cup / harga_jual) * 100) as avg_margin')
            ->value('avg_margin') ?? 0;

        return view('menu.index', compact('menus', 'kategoriList', 'totalMenu', 'totalAktif', 'avgMargin'));
    }

    public function create()
    {
        $bahanBakuList = BahanBaku::orderBy('nama_bahan', 'asc')->get();
        return view('menu.create', compact('bahanBakuList'));
    }

    public function store(StoreMenuRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $menu = Menu::create([
                'nama_menu' => $validated['nama_menu'],
                'kategori' => $validated['kategori'],
                'harga_jual' => (float) $validated['harga_jual'],
                'biaya_lain' => isset($validated['biaya_lain']) ? (float)$validated['biaya_lain'] : 0,
                'is_active' => $validated['is_active'] ?? true,
                'cost_per_cup' => 0,
                'keuntungan_per_cup' => 0,
            ]);

            if (!empty($validated['resep'])) {
                foreach ($validated['resep'] as $item) {
                    if (!empty($item['bahan_baku_id']) && !empty($item['jumlah_pemakaian'])) {
                        Resep::create([
                            'menu_id' => $menu->id,
                            'bahan_baku_id' => $item['bahan_baku_id'],
                            'jumlah_pemakaian' => (float) $item['jumlah_pemakaian'],
                        ]);
                    }
                }
            }

            // Hitung otomatis COGS & Margin
            $menu->calculateCost();

            ActivityLog::log(
                'BUAT_MENU',
                "Membuat menu baru: {$menu->nama_menu} (HPP: Rp {$menu->cost_per_cup}, Jual: Rp {$menu->harga_jual})",
                ['menu_id' => $menu->id]
            );
        });

        return redirect()->route('menu.index')->with('success', "Menu {$validated['nama_menu']} dan kalkulasi HPP berhasil disimpan.");
    }

    public function edit(Menu $menu)
    {
        $menu->load('resep.bahanBaku');
        $bahanBakuList = BahanBaku::orderBy('nama_bahan', 'asc')->get();
        return view('menu.edit', compact('menu', 'bahanBakuList'));
    }

    public function update(StoreMenuRequest $request, Menu $menu)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $menu) {
            $menu->update([
                'nama_menu' => $validated['nama_menu'],
                'kategori' => $validated['kategori'],
                'harga_jual' => (float) $validated['harga_jual'],
                'biaya_lain' => isset($validated['biaya_lain']) ? (float)$validated['biaya_lain'] : 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Hapus resep lama dan pasang yang baru
            $menu->resep()->delete();

            if (!empty($validated['resep'])) {
                foreach ($validated['resep'] as $item) {
                    if (!empty($item['bahan_baku_id']) && !empty($item['jumlah_pemakaian'])) {
                        Resep::create([
                            'menu_id' => $menu->id,
                            'bahan_baku_id' => $item['bahan_baku_id'],
                            'jumlah_pemakaian' => (float) $item['jumlah_pemakaian'],
                        ]);
                    }
                }
            }

            // Hitung ulang HPP
            $menu->calculateCost();

            ActivityLog::log(
                'UPDATE_MENU',
                "Memperbarui menu: {$menu->nama_menu} (HPP: Rp {$menu->cost_per_cup}, Jual: Rp {$menu->harga_jual})",
                ['menu_id' => $menu->id]
            );
        });

        return redirect()->route('menu.index')->with('success', "Menu {$menu->nama_menu} berhasil diperbarui.");
    }

    public function toggleActive(Menu $menu)
    {
        $menu->update(['is_active' => !$menu->is_active]);
        $status = $menu->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Status menu {$menu->nama_menu} berhasil {$status}.");
    }

    public function destroy(Menu $menu)
    {
        $nama = $menu->nama_menu;
        $menu->delete();

        ActivityLog::log('HAPUS_MENU', "Menghapus menu: {$nama}");

        return redirect()->route('menu.index')->with('success', "Menu {$nama} berhasil dihapus.");
    }

    public function exportExcel()
    {
        $fileName = 'rekap-menu-cogs-clover-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new MenuCostingExport, $fileName);
    }
}
