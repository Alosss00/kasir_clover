<?php

use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Kasir Clover POS & Costing System
|--------------------------------------------------------------------------
*/

// Landing Page Portal
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin() ? redirect()->route('dashboard') : redirect()->route('kasir.index');
    }
    return view('welcome');
});

// Dashboard Router
Route::get('/dashboard', function () {
    if (Auth::user()->isKasir()) {
        return redirect()->route('kasir.index');
    }
    return redirect()->route('bahan-baku.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\KasirController;

// ==========================================
// MODUL 1 & 4: KASIR / POS (Admin & Kasir)
// ==========================================
Route::middleware(['auth', 'role:admin,kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('index');
    Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
    Route::get('/struk/{transaksi}', [KasirController::class, 'cetakStruk'])->name('struk');
});

// ==========================================
// MODUL 2, 3, 5, 6: ADMIN INVENTORY & REPORTS
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // MODUL 2: BAHAN BAKU & STOK (WAC)
    Route::prefix('bahan-baku')->name('bahan-baku.')->group(function () {
        Route::get('/', [BahanBakuController::class, 'index'])->name('index');
        Route::post('/', [BahanBakuController::class, 'store'])->name('store');
        Route::get('/{bahanBaku}/histori', [BahanBakuController::class, 'histori'])->name('histori');
        Route::delete('/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('destroy');
    });

    // MODUL 3 & 6: MENU, RESEP & REKAP EXCEL COGS
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::get('/export-excel', [MenuController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::patch('/{menu}/toggle-active', [MenuController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
    });

    // MODUL 5: LAPORAN PENJUALAN, EXCEL & PDF
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('/penjualan/excel', [LaporanController::class, 'exportExcel'])->name('penjualan.excel');
        Route::get('/penjualan/pdf', [LaporanController::class, 'exportPdf'])->name('penjualan.pdf');
    });

});

require __DIR__.'/auth.php';
