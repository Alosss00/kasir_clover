<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\BahanBakuHistori;
use App\Models\Menu;
use App\Models\Resep;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@clover.com'],
            [
                'name' => 'Admin Clover',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@clover.com'],
            [
                'name' => 'Kasir Clover',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'is_active' => true,
            ]
        );

        // 2. Bahan Baku Awal
        // Kopi Robusta / Arabica Blend: 1000gr @ Rp180.000 -> Rp180/gr
        $kopi = BahanBaku::create([
            'nama_bahan' => 'Biji Kopi House Blend',
            'satuan' => 'gr',
            'stok_total' => 5000.00,
            'total_harga_beli' => 900000.00,
            'harga_per_satuan' => 180.0000,
            'stok_minimum' => 500.00,
        ]);
        BahanBakuHistori::create([
            'bahan_baku_id' => $kopi->id,
            'jumlah_ditambahkan' => 5000.00,
            'harga_beli' => 900000.00,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Stok Awal 5kg Kopi',
        ]);

        // Susu UHT: 12000ml @ Rp276.000 -> Rp23/ml
        $susu = BahanBaku::create([
            'nama_bahan' => 'Susu UHT Full Cream',
            'satuan' => 'ml',
            'stok_total' => 12000.00,
            'total_harga_beli' => 276000.00,
            'harga_per_satuan' => 23.0000,
            'stok_minimum' => 2000.00,
        ]);
        BahanBakuHistori::create([
            'bahan_baku_id' => $susu->id,
            'jumlah_ditambahkan' => 12000.00,
            'harga_beli' => 276000.00,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Stok Awal 12 Karton Susu',
        ]);

        // Sirup Gula Aren: 1000ml @ Rp66.666,67 -> Rp66.67/ml (30ml = Rp2.000)
        $gulaAren = BahanBaku::create([
            'nama_bahan' => 'Sirup Gula Aren Cair',
            'satuan' => 'ml',
            'stok_total' => 3000.00,
            'total_harga_beli' => 200000.00,
            'harga_per_satuan' => 66.6667,
            'stok_minimum' => 500.00,
        ]);
        BahanBakuHistori::create([
            'bahan_baku_id' => $gulaAren->id,
            'jumlah_ditambahkan' => 3000.00,
            'harga_beli' => 200000.00,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Stok Awal 3 Liter Gula Aren',
        ]);

        // Bubuk Matcha Premium: 1000gr @ Rp250.000 -> Rp250/gr
        $matcha = BahanBaku::create([
            'nama_bahan' => 'Bubuk Matcha Premium',
            'satuan' => 'gr',
            'stok_total' => 2000.00,
            'total_harga_beli' => 500000.00,
            'harga_per_satuan' => 250.0000,
            'stok_minimum' => 300.00,
        ]);
        BahanBakuHistori::create([
            'bahan_baku_id' => $matcha->id,
            'jumlah_ditambahkan' => 2000.00,
            'harga_beli' => 500000.00,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Stok Awal 2kg Matcha',
        ]);

        // Bubuk Cokelat: 1000gr @ Rp150.000 -> Rp150/gr
        $cokelat = BahanBaku::create([
            'nama_bahan' => 'Bubuk Cokelat Dark',
            'satuan' => 'gr',
            'stok_total' => 2000.00,
            'total_harga_beli' => 300000.00,
            'harga_per_satuan' => 150.0000,
            'stok_minimum' => 300.00,
        ]);
        BahanBakuHistori::create([
            'bahan_baku_id' => $cokelat->id,
            'jumlah_ditambahkan' => 2000.00,
            'harga_beli' => 300000.00,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Stok Awal 2kg Cokelat',
        ]);

        // 3. Menu & Resep
        // Menu 1: Kopi Susu Aren Spesial (Sesuai contoh user: Kopi 18gr (3240) + Susu 100ml (2300) + Gula Aren 30ml (2000) + Cup/Biaya Lain (4000) = Cost 11.540, Harga 25.000 -> Untung 13.460)
        $menu1 = Menu::create([
            'nama_menu' => 'Kopi Susu Aren Clover',
            'kategori' => 'Coffee',
            'harga_jual' => 25000.00,
            'biaya_lain' => 4000.00, // Cup (1500) + Sedotan/Lain (2500)
            'cost_per_cup' => 11540.00,
            'keuntungan_per_cup' => 13460.00,
            'is_active' => true,
        ]);
        Resep::create(['menu_id' => $menu1->id, 'bahan_baku_id' => $kopi->id, 'jumlah_pemakaian' => 18.00]);
        Resep::create(['menu_id' => $menu1->id, 'bahan_baku_id' => $susu->id, 'jumlah_pemakaian' => 100.00]);
        Resep::create(['menu_id' => $menu1->id, 'bahan_baku_id' => $gulaAren->id, 'jumlah_pemakaian' => 30.00]);

        // Menu 2: Iced Americano (Kopi 18gr + Biaya lain 2000) -> Cost 5240, Harga 18000 -> Untung 12760
        $menu2 = Menu::create([
            'nama_menu' => 'Iced Americano Classic',
            'kategori' => 'Coffee',
            'harga_jual' => 18000.00,
            'biaya_lain' => 2000.00,
            'cost_per_cup' => 5240.00,
            'keuntungan_per_cup' => 12760.00,
            'is_active' => true,
        ]);
        Resep::create(['menu_id' => $menu2->id, 'bahan_baku_id' => $kopi->id, 'jumlah_pemakaian' => 18.00]);

        // Menu 3: Matcha Latte Creamy (Matcha 20gr @ 250 = 5000 + Susu 120ml @ 23 = 2760 + Gula Aren 20ml @ 66.67 = 1333.33 + Biaya lain 3000 = Cost 12093.33, Harga 28000)
        $menu3 = Menu::create([
            'nama_menu' => 'Matcha Latte Creamy',
            'kategori' => 'Non-Coffee',
            'harga_jual' => 28000.00,
            'biaya_lain' => 3000.00,
            'cost_per_cup' => 12093.33,
            'keuntungan_per_cup' => 15906.67,
            'is_active' => true,
        ]);
        Resep::create(['menu_id' => $menu3->id, 'bahan_baku_id' => $matcha->id, 'jumlah_pemakaian' => 20.00]);
        Resep::create(['menu_id' => $menu3->id, 'bahan_baku_id' => $susu->id, 'jumlah_pemakaian' => 120.00]);
        Resep::create(['menu_id' => $menu3->id, 'bahan_baku_id' => $gulaAren->id, 'jumlah_pemakaian' => 20.00]);

        // Menu 4: Signature Dark Chocolate (Cokelat 30gr @ 150 = 4500 + Susu 120ml @ 23 = 2760 + Biaya lain 2500 = Cost 9760, Harga 24000)
        $menu4 = Menu::create([
            'nama_menu' => 'Signature Dark Chocolate',
            'kategori' => 'Non-Coffee',
            'harga_jual' => 24000.00,
            'biaya_lain' => 2500.00,
            'cost_per_cup' => 9760.00,
            'keuntungan_per_cup' => 14240.00,
            'is_active' => true,
        ]);
        Resep::create(['menu_id' => $menu4->id, 'bahan_baku_id' => $cokelat->id, 'jumlah_pemakaian' => 30.00]);
        Resep::create(['menu_id' => $menu4->id, 'bahan_baku_id' => $susu->id, 'jumlah_pemakaian' => 120.00]);
    }
}
