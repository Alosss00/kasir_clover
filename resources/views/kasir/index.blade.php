<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>☕ Terminal Kasir POS</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih menu, cek ketersediaan porsi/cup, dan proses transaksi instan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Kasir: {{ Auth::user()->name }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- KASIR POS CONTAINER -->
    <div id="pos-app" class="flex flex-col lg:flex-row gap-6 items-start">

        <!-- SISI KIRI: Katalog Menu & Pencarian (Lebar Fleksibel) -->
        <div class="w-full lg:flex-1 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col">
            
            <!-- Pencarian & Filter Kategori -->
            <div class="space-y-3 mb-5">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        id="search-input" 
                        oninput="filterMenu()" 
                        placeholder="Cari menu kopi, minuman, makanan..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    >
                </div>

                <!-- Tombol Kategori -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none" id="category-buttons">
                    <button 
                        type="button" 
                        onclick="setCategory('all')" 
                        class="cat-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-emerald-600 text-white shadow-xs cursor-pointer" 
                        data-cat="all"
                    >
                        Semua Kategori
                    </button>
                    @foreach($categories as $cat)
                        <button 
                            type="button" 
                            onclick="setCategory('{{ $cat }}')" 
                            class="cat-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer" 
                            data-cat="{{ $cat }}"
                        >
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Kartu Menu -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5" id="menu-grid">
                @forelse($menuList as $m)
                    @php
                        $stock = $m->available_stock;
                        $isOutOfStock = ($stock !== null && $stock <= 0);
                    @endphp
                    <div 
                        onclick="handleCardClick({{ $m->id }}, '{{ addslashes($m->nama_menu) }}', {{ $m->harga_jual }}, '{{ $m->kategori }}', {{ $m->cost_per_cup ?? 0 }}, {{ $stock !== null ? $stock : 'null' }})"
                        class="menu-item-card group p-4 rounded-xl border transition-all text-left flex flex-col justify-between shadow-xs select-none relative overflow-hidden {{ $isOutOfStock ? 'bg-slate-100/80 border-slate-200 opacity-60 cursor-not-allowed' : 'bg-slate-50 hover:bg-emerald-50/60 border-slate-200 hover:border-emerald-400 cursor-pointer active:scale-98' }}"
                        data-id="{{ $m->id }}"
                        data-name="{{ strtolower($m->nama_menu) }}"
                        data-category="{{ $m->kategori }}"
                        data-stock="{{ $stock !== null ? $stock : 'unlimited' }}"
                    >
                        <div>
                            <!-- Badges: Kategori & Sisa Cup -->
                            <div class="flex items-center justify-between gap-1 flex-wrap mb-2">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white text-slate-500 border border-slate-200">
                                    {{ $m->kategori }}
                                </span>

                                @if($stock === null)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200/70 text-slate-700">
                                        ∞ Bebas
                                    </span>
                                @elseif($stock <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">
                                        ❌ Habis
                                    </span>
                                @elseif($stock <= 10)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">
                                        ⚠️ Sisa {{ $stock }} Cup
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        ☕ {{ $stock }} Cup
                                    </span>
                                @endif
                            </div>

                            <h4 class="font-bold text-slate-900 text-sm {{ $isOutOfStock ? 'text-slate-500' : 'group-hover:text-emerald-700' }} transition-colors line-clamp-2 leading-snug">
                                {{ $m->nama_menu }}
                            </h4>
                        </div>

                        <div class="mt-4 pt-2 border-t border-slate-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-sm font-extrabold {{ $isOutOfStock ? 'text-slate-500' : 'text-emerald-600' }}">
                                    Rp {{ number_format($m->harga_jual, 0, ',', '.') }}
                                </span>
                                @if($stock !== null && $stock > 0)
                                    <span class="block text-[10px] text-slate-400 font-medium">bisa dibuat {{ $stock }} cup</span>
                                @elseif($stock === 0)
                                    <span class="block text-[10px] text-rose-500 font-bold">stok bahan kosong</span>
                                @endif
                            </div>

                            @if($isOutOfStock)
                                <div class="w-7 h-7 rounded-lg bg-slate-300 text-slate-500 flex items-center justify-center font-bold text-xs">
                                    ✕
                                </div>
                            @else
                                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs group-hover:bg-emerald-700 transition-colors">
                                    +
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-400">
                        <div class="text-sm font-semibold text-slate-600">Belum ada menu yang aktif</div>
                        <p class="text-xs text-slate-400 mt-1">Silakan tambahkan menu terlebih dahulu di menu manajemen.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pesan Menu Kosong saat Filter -->
            <div id="no-menu-found" class="hidden py-16 text-center text-slate-400">
                <div class="text-sm font-semibold text-slate-600">Menu tidak ditemukan</div>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian atau kategori yang lain.</p>
            </div>
        </div>

        <!-- SISI KANAN: Keranjang & Ringkasan Checkout (Lebar Tetap) -->
        <div class="w-full lg:w-96 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col shrink-0 sticky top-20">
            
            <!-- Header Keranjang -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs" id="cart-badge-count">
                        0
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Keranjang Pesanan</h3>
                    </div>
                </div>

                <button 
                    type="button" 
                    onclick="clearCart()" 
                    id="btn-clear-cart" 
                    class="hidden text-xs text-rose-600 hover:text-rose-700 font-bold hover:underline cursor-pointer"
                >
                    Kosongkan
                </button>
            </div>

            <!-- Input Form Nama Customer -->
            <div class="py-2.5 border-b border-slate-100 bg-slate-50/70 -mx-5 px-5">
                <div class="flex items-center justify-between mb-1">
                    <label for="cart-customer-input" class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Nama Customer</span>
                    </label>
                </div>
                <div class="relative">
                    <input 
                        type="text" 
                        id="cart-customer-input" 
                        oninput="syncCustomerName(this.value)" 
                        placeholder="Nama Pemesan / Pelanggan" 
                        value="Pelanggan Umum"
                        class="w-full px-3 py-1.5 text-xs rounded-xl bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all font-semibold"
                    >
                </div>
                <!-- Shortcut Quick Chips -->
                <div class="flex items-center gap-1.5 mt-1.5 overflow-x-auto scrollbar-none pb-0.5">
                    <button type="button" onclick="setQuickCustomer('Pelanggan Umum')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-[10px] font-semibold text-slate-600 transition-colors whitespace-nowrap cursor-pointer shadow-2xs">Umum</button>
                    <button type="button" onclick="setQuickCustomer('Dine In')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-[10px] font-semibold text-slate-600 transition-colors whitespace-nowrap cursor-pointer shadow-2xs">Dine In</button>
                    <button type="button" onclick="setQuickCustomer('Take Away')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-[10px] font-semibold text-slate-600 transition-colors whitespace-nowrap cursor-pointer shadow-2xs">Take Away</button>
                    <button type="button" onclick="setQuickCustomer('Meja 01')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-[10px] font-semibold text-slate-600 transition-colors whitespace-nowrap cursor-pointer shadow-2xs">Meja 1</button>
                    <button type="button" onclick="setQuickCustomer('Meja 02')" class="px-2 py-0.5 rounded-lg bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 border border-slate-200 text-[10px] font-semibold text-slate-600 transition-colors whitespace-nowrap cursor-pointer shadow-2xs">Meja 2</button>
                </div>
            </div>

            <!-- Daftar Item Keranjang -->
            <div class="py-3 space-y-2 max-h-[340px] overflow-y-auto pr-1" id="cart-items-container">
                <!-- Cart items generated by JS -->
            </div>

            <!-- Empty Cart Placeholder -->
            <div id="cart-empty-placeholder" class="py-12 flex flex-col items-center justify-center text-center text-slate-400">
                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-2xl mb-2">
                    🛒
                </div>
                <div class="text-xs font-semibold text-slate-600">Keranjang Masih Kosong</div>
                <p class="text-[11px] text-slate-400 mt-0.5">Klik menu di sebelah kiri untuk menambahkan pesanan.</p>
            </div>

            <!-- Ringkasan Total & Tombol Bayar -->
            <div class="pt-4 border-t border-slate-200 space-y-3">
                <div class="flex justify-between items-center text-xs text-slate-600">
                    <span>Total Item:</span>
                    <span class="font-bold text-slate-800" id="cart-total-qty">0 Cup / Pcs</span>
                </div>

                <div class="flex justify-between items-center text-base font-bold text-slate-900 pt-2 border-t border-dashed border-slate-200">
                    <span>Grand Total:</span>
                    <span class="text-xl text-emerald-600 font-black" id="cart-total-price">
                        Rp 0
                    </span>
                </div>

                <button 
                    type="button" 
                    id="btn-pay" 
                    onclick="openPaymentModal()" 
                    disabled
                    class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98"
                >
                    <span>BAYAR SEKARANG (F8)</span>
                </button>
            </div>

            <!-- Riwayat Transaksi Cepat Kasir -->
            @if(isset($recentTransactions) && count($recentTransactions) > 0)
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Transaksi Terakhir Hari Ini</h4>
                    <div class="space-y-1.5 max-h-36 overflow-y-auto">
                        @foreach($recentTransactions as $rt)
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $rt->kode_transaksi }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $rt->tanggal_transaksi->format('H:i') }} • {{ strtoupper($rt->metode_pembayaran) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-600 block">Rp {{ number_format($rt->total_harga, 0, ',', '.') }}</span>
                                    <a href="{{ route('kasir.struk', $rt->id) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline">Cetak Struk</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- MODAL PEMBAYARAN -->
    <div id="payment-modal" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" onclick="closePaymentModal()"></div>

        <!-- Modal Content -->
        <div class="relative bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-2xl z-10">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pembayaran Pesanan</h3>
                    <p class="text-xs text-slate-500">Pilih metode & tentukan nominal pembayaran</p>
                </div>
                <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Total Tagihan Display -->
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-center mb-4">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Tagihan</span>
                <div class="text-2xl font-black text-emerald-700 mt-0.5" id="modal-grand-total">
                    Rp 0
                </div>
            </div>

            <!-- Pilihan Metode Pembayaran -->
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        type="button" 
                        id="btn-method-cash"
                        onclick="setPaymentMethod('cash')" 
                        class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-emerald-600 text-white border-emerald-600 shadow-xs cursor-pointer"
                    >
                        <span>💵 Tunai (Cash)</span>
                    </button>

                    <button 
                        type="button" 
                        id="btn-method-qris"
                        onclick="setPaymentMethod('debit_qris')" 
                        class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50 cursor-pointer"
                    >
                        <span>📱 Debit / QRIS</span>
                    </button>
                </div>

                <!-- Bagian Pembayaran Cash -->
                <div id="cash-payment-section" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uang Diterima (Rp)</label>
                        <input 
                            type="number" 
                            id="cash-amount-input" 
                            oninput="calculateChange()" 
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xl font-black focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                            placeholder="0"
                        >
                    </div>

                    <!-- Preset Nominal Cepat -->
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="setCashExact()" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200 cursor-pointer">
                            Uang Pas
                        </button>
                        <button type="button" onclick="setCashPreset(20000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200 cursor-pointer">
                            20.000
                        </button>
                        <button type="button" onclick="setCashPreset(50000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200 cursor-pointer">
                            50.000
                        </button>
                        <button type="button" onclick="setCashPreset(100000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200 cursor-pointer">
                            100.000
                        </button>
                    </div>

                    <!-- Display Kembalian -->
                    <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-between" id="change-box">
                        <span class="text-xs font-bold text-slate-600 uppercase">Kembalian</span>
                        <span class="text-lg font-black text-slate-900" id="cash-change-display">
                            Rp 0
                        </span>
                    </div>
                </div>

                <!-- Bagian QRIS Info -->
                <div id="qris-payment-section" class="hidden p-4 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 text-xs font-medium text-center">
                    <div class="text-2xl mb-1">📱</div>
                    Silakan scan QRIS atau gesek kartu debit pada mesin EDC. Nominal tagihan sesuai dengan total transaksi.
                </div>

                <!-- Nama Customer -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Customer / Pelanggan</label>
                    <input 
                        type="text" 
                        id="modal-customer-input" 
                        oninput="syncCustomerName(this.value)" 
                        placeholder="Contoh: Budi / Meja 05" 
                        value="Pelanggan Umum"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-semibold focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none"
                    >
                </div>

                <!-- Catatan Pesanan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <input 
                        type="text" 
                        id="order-note-input" 
                        placeholder="Contoh: Less ice, Extra shot" 
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                    >
                </div>
            </div>

            <!-- Tombol Konfirmasi -->
            <div class="mt-6 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closePaymentModal()" 
                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    id="btn-confirm-checkout" 
                    onclick="submitCheckout()" 
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 cursor-pointer transition-all flex items-center gap-1.5"
                >
                    <span>Konfirmasi & Bayar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL STRUK SUKSES & CETAK -->
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden overflow-y-auto flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeReceiptModal()"></div>

        <!-- Modal Content -->
        <div class="relative bg-white border border-slate-200 rounded-2xl max-w-sm w-full p-5 shadow-2xl z-10">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200 mb-3">
                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Transaksi Berhasil
                </span>
                <button type="button" onclick="closeReceiptModal()" class="text-slate-400 hover:text-slate-700 text-xs font-bold cursor-pointer">
                    Tutup
                </button>
            </div>

            <!-- Preview Struk Kasir Thermal 58mm -->
            <div id="printable-receipt" class="bg-white text-slate-900 p-4 rounded-xl font-mono text-xs border border-slate-200 shadow-inner">
                <div class="text-center border-b border-dashed border-slate-300 pb-2 mb-2">
                    <div class="text-sm font-bold tracking-wider">KASIR CLOVER</div>
                    <div class="text-[10px] text-slate-500">Coffee & Roastery</div>
                </div>

                <div class="space-y-0.5 text-[11px] border-b border-dashed border-slate-300 pb-2 mb-2" id="receipt-header-meta">
                    <!-- Meta info inserted by JS -->
                </div>

                <div class="space-y-1.5 border-b border-dashed border-slate-300 pb-2 mb-2" id="receipt-items-list">
                    <!-- Items inserted by JS -->
                </div>

                <div class="space-y-1 text-[11px]" id="receipt-total-section">
                    <!-- Totals inserted by JS -->
                </div>

                <div class="text-center pt-3 mt-2 border-t border-dashed border-slate-300 text-[10px] text-slate-400">
                    <p>Terima kasih atas pesanan Anda!</p>
                </div>
            </div>

            <!-- Tombol Aksi Struk -->
            <div class="mt-4 grid grid-cols-2 gap-2">
                <button 
                    type="button" 
                    id="btn-print-receipt"
                    onclick="printThermalReceipt()" 
                    class="py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center justify-center gap-1 cursor-pointer transition-colors shadow-xs"
                >
                    <span>🖨️ Cetak Struk</span>
                </button>
                <button 
                    type="button" 
                    onclick="closeReceiptModal()" 
                    class="py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer transition-colors"
                >
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- POS INTERACTION LOGIC -->
    <script>
        // State Store
        let cart = [];
        let currentCategory = 'all';
        let paymentMethod = 'cash';
        let customerName = 'Pelanggan Umum';
        let lastCreatedTransaction = null;

        const checkoutUrl = "{{ route('kasir.checkout') }}";
        const csrfToken = "{{ csrf_token() }}";

        function syncCustomerName(val) {
            customerName = val;
            const cInput = document.getElementById('cart-customer-input');
            const mInput = document.getElementById('modal-customer-input');
            if (cInput && cInput.value !== val) cInput.value = val;
            if (mInput && mInput.value !== val) mInput.value = val;
        }

        function setQuickCustomer(val) {
            syncCustomerName(val);
            const cInput = document.getElementById('cart-customer-input');
            if (cInput) cInput.focus();
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        // 1. HANDLE CARD CLICK WITH STOCK CHECK
        function handleCardClick(menuId, nama, harga, kategori, costPerCup, maxStock) {
            if (maxStock !== null && maxStock <= 0) {
                alert(`Maaf, stok bahan baku untuk "${nama}" saat ini habis (0 Cup). Silakan restok bahan baku di menu inventori.`);
                return;
            }
            addToCart(menuId, nama, harga, kategori, costPerCup, maxStock);
        }

        // 2. ADD TO CART
        function addToCart(menuId, nama, harga, kategori, costPerCup, maxStock) {
            const existingIndex = cart.findIndex(item => item.menu_id === menuId);
            if (existingIndex > -1) {
                const currentQty = cart[existingIndex].qty;
                if (maxStock !== null && currentQty >= maxStock) {
                    alert(`Maksimal pesanan "${nama}" saat ini adalah ${maxStock} cup sesuai sisa stok bahan baku yang ada.`);
                    return;
                }
                cart[existingIndex].qty += 1;
                cart[existingIndex].subtotal = cart[existingIndex].qty * cart[existingIndex].harga;
            } else {
                cart.push({
                    menu_id: menuId,
                    nama: nama,
                    harga: Number(harga),
                    kategori: kategori,
                    cost_per_cup: Number(costPerCup),
                    max_stock: maxStock,
                    qty: 1,
                    subtotal: Number(harga)
                });
            }
            renderCart();
        }

        // 3. UPDATE ITEM QUANTITY
        function updateQty(index, delta) {
            if (cart[index]) {
                const item = cart[index];
                const newQty = item.qty + delta;

                if (delta > 0 && item.max_stock !== null && newQty > item.max_stock) {
                    alert(`Sisa stok bahan baku untuk "${item.nama}" hanya cukup untuk ${item.max_stock} cup.`);
                    return;
                }

                if (newQty > 0) {
                    item.qty = newQty;
                    item.subtotal = newQty * item.harga;
                } else {
                    cart.splice(index, 1);
                }
                renderCart();
            }
        }

        // 4. REMOVE ITEM
        function removeItem(index) {
            if (cart[index]) {
                cart.splice(index, 1);
                renderCart();
            }
        }

        // 5. CLEAR CART
        function clearCart() {
            if (cart.length > 0) {
                if (confirm('Kosongkan seluruh item di keranjang pesanan?')) {
                    cart = [];
                    renderCart();
                }
            }
        }

        // 6. RENDER CART UI
        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const placeholder = document.getElementById('cart-empty-placeholder');
            const badge = document.getElementById('cart-badge-count');
            const clearBtn = document.getElementById('btn-clear-cart');
            const totalQtyEl = document.getElementById('cart-total-qty');
            const totalPriceEl = document.getElementById('cart-total-price');
            const payBtn = document.getElementById('btn-pay');

            let totalQty = 0;
            let grandTotal = 0;

            if (cart.length === 0) {
                container.innerHTML = '';
                placeholder.classList.remove('hidden');
                clearBtn.classList.add('hidden');
                payBtn.disabled = true;
                badge.innerText = '0';
                totalQtyEl.innerText = '0 Cup / Pcs';
                totalPriceEl.innerText = 'Rp 0';
                return;
            }

            placeholder.classList.add('hidden');
            clearBtn.classList.remove('hidden');
            payBtn.disabled = false;

            let html = '';
            cart.forEach((item, index) => {
                totalQty += item.qty;
                grandTotal += item.subtotal;

                const isMaxReached = (item.max_stock !== null && item.qty >= item.max_stock);

                html += `
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2 transition-all">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">${item.nama}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                <span>@ ${formatRupiah(item.harga)}</span>
                                ${item.max_stock !== null ? `<span class="text-[10px] text-slate-400 font-medium">(Maks: ${item.max_stock})</span>` : ''}
                            </div>
                        </div>

                        <!-- Tombol Qty -->
                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                onclick="updateQty(${index}, -1)" 
                                class="w-6 h-6 rounded bg-white hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs transition-colors cursor-pointer"
                            >-</button>
                            <span class="text-xs font-bold text-slate-900 w-5 text-center">${item.qty}</span>
                            <button 
                                type="button" 
                                onclick="updateQty(${index}, 1)" 
                                ${isMaxReached ? 'disabled title="Maksimal porsi tercapai"' : ''}
                                class="w-6 h-6 rounded bg-white hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs transition-colors cursor-pointer"
                            >+</button>
                        </div>

                        <!-- Subtotal & Hapus -->
                        <div class="text-right">
                            <div class="font-bold text-xs text-slate-900">${formatRupiah(item.subtotal)}</div>
                            <button 
                                type="button" 
                                onclick="removeItem(${index})" 
                                class="text-[10px] text-rose-500 hover:text-rose-700 hover:underline mt-0.5 cursor-pointer"
                            >Hapus</button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            badge.innerText = totalQty;
            totalQtyEl.innerText = `${totalQty} Cup / Pcs`;
            totalPriceEl.innerText = formatRupiah(grandTotal);
        }

        // 7. FILTER MENU & SEARCH
        function filterMenu() {
            const query = document.getElementById('search-input').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.menu-item-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const cat = card.getAttribute('data-category');

                const matchSearch = name.includes(query);
                const matchCategory = (currentCategory === 'all' || cat === currentCategory);

                if (matchSearch && matchCategory) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noMenu = document.getElementById('no-menu-found');
            if (visibleCount === 0) {
                noMenu.classList.remove('hidden');
            } else {
                noMenu.classList.add('hidden');
            }
        }

        function setCategory(cat) {
            currentCategory = cat;
            document.querySelectorAll('.cat-btn').forEach(btn => {
                if (btn.getAttribute('data-cat') === cat) {
                    btn.className = 'cat-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-emerald-600 text-white shadow-xs cursor-pointer';
                } else {
                    btn.className = 'cat-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer';
                }
            });
            filterMenu();
        }

        // 8. CHECKOUT MODAL & PAYMENT
        function getGrandTotal() {
            return cart.reduce((acc, item) => acc + item.subtotal, 0);
        }

        function openPaymentModal() {
            if (cart.length === 0) return;

            const total = getGrandTotal();
            document.getElementById('modal-grand-total').innerText = formatRupiah(total);
            const cInput = document.getElementById('cart-customer-input');
            if (cInput) syncCustomerName(cInput.value);
            document.getElementById('payment-modal').classList.remove('hidden');

            setPaymentMethod('cash');
            setCashExact();
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }

        function setPaymentMethod(method) {
            paymentMethod = method;
            const btnCash = document.getElementById('btn-method-cash');
            const btnQris = document.getElementById('btn-method-qris');
            const secCash = document.getElementById('cash-payment-section');
            const secQris = document.getElementById('qris-payment-section');

            if (method === 'cash') {
                btnCash.className = 'p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-emerald-600 text-white border-emerald-600 shadow-xs cursor-pointer';
                btnQris.className = 'p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50 cursor-pointer';
                secCash.classList.remove('hidden');
                secQris.classList.add('hidden');
                calculateChange();
            } else {
                btnQris.className = 'p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-emerald-600 text-white border-emerald-600 shadow-xs cursor-pointer';
                btnCash.className = 'p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50 cursor-pointer';
                secCash.classList.add('hidden');
                secQris.classList.remove('hidden');
            }
        }

        function setCashExact() {
            const total = getGrandTotal();
            const input = document.getElementById('cash-amount-input');
            input.value = total;
            calculateChange();
            input.focus();
        }

        function setCashPreset(amount) {
            const input = document.getElementById('cash-amount-input');
            input.value = amount;
            calculateChange();
            input.focus();
        }

        function calculateChange() {
            if (paymentMethod !== 'cash') return;

            const total = getGrandTotal();
            const bayar = Number(document.getElementById('cash-amount-input').value) || 0;
            const kembalian = bayar - total;
            const changeDisplay = document.getElementById('cash-change-display');
            const changeBox = document.getElementById('change-box');

            if (kembalian >= 0) {
                changeDisplay.innerText = formatRupiah(kembalian);
                changeDisplay.className = 'text-lg font-black text-slate-900';
                changeBox.className = 'p-3.5 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-between';
            } else {
                changeDisplay.innerText = 'Kurang ' + formatRupiah(Math.abs(kembalian));
                changeDisplay.className = 'text-sm font-bold text-rose-600';
                changeBox.className = 'p-3.5 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between';
            }
        }

        // 9. SUBMIT CHECKOUT TO BACKEND
        async function submitCheckout() {
            if (cart.length === 0) {
                alert('Keranjang belanja kosong.');
                return;
            }

            const total = getGrandTotal();
            const cashInput = Number(document.getElementById('cash-amount-input').value) || 0;
            const noteInput = document.getElementById('order-note-input').value.trim();

            if (paymentMethod === 'cash' && cashInput < total) {
                alert('Nominal uang bayar kurang dari total belanja.');
                document.getElementById('cash-amount-input').focus();
                return;
            }

            const confirmBtn = document.getElementById('btn-confirm-checkout');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses Transaksi...
            `;

            try {
                const response = await fetch(checkoutUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: cart.map(item => ({
                            menu_id: item.menu_id,
                            qty: item.qty
                        })),
                        nama_customer: customerName || 'Pelanggan Umum',
                        metode_pembayaran: paymentMethod,
                        jumlah_bayar: paymentMethod === 'cash' ? cashInput : total,
                        catatan: noteInput
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Gagal memproses transaksi.');
                }

                // Sukses!
                lastCreatedTransaction = data.transaksi;
                closePaymentModal();
                showReceiptModal(data.transaksi);

                // Reset Cart
                cart = [];
                renderCart();
                document.getElementById('order-note-input').value = '';
                syncCustomerName('Pelanggan Umum');

            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `<span>Konfirmasi & Bayar</span>`;
            }
        }

        // 10. RECEIPT MODAL & PRINTING
        function showReceiptModal(trx) {
            const metaContainer = document.getElementById('receipt-header-meta');
            const itemsContainer = document.getElementById('receipt-items-list');
            const totalContainer = document.getElementById('receipt-total-section');

            const dt = new Date(trx.tanggal_transaksi || new Date());
            const dateStr = dt.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' +
                            dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            metaContainer.innerHTML = `
                <div class="flex justify-between">
                    <span>No:</span>
                    <span class="font-bold">${trx.kode_transaksi}</span>
                </div>
                <div class="flex justify-between">
                    <span>Customer:</span>
                    <span class="font-bold text-slate-900">${trx.nama_customer || 'Pelanggan Umum'}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tgl:</span>
                    <span>${dateStr}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>${trx.kasir ? trx.kasir.name : 'Kasir'}</span>
                </div>
                ${trx.catatan ? `
                <div class="flex justify-between">
                    <span>Note:</span>
                    <span>${trx.catatan}</span>
                </div>` : ''}
            `;

            let itemsHtml = '';
            (trx.details || []).forEach(d => {
                itemsHtml += `
                    <div>
                        <div class="font-bold">${d.nama_menu_snapshot}</div>
                        <div class="flex justify-between text-[10px] text-slate-600">
                            <span>${d.qty}x @ ${formatRupiah(d.harga_satuan_snapshot)}</span>
                            <span>${formatRupiah(d.subtotal)}</span>
                        </div>
                    </div>
                `;
            });
            itemsContainer.innerHTML = itemsHtml;

            totalContainer.innerHTML = `
                <div class="flex justify-between font-bold text-xs pt-1">
                    <span>TOTAL:</span>
                    <span>${formatRupiah(trx.total_harga)}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Metode:</span>
                    <span class="uppercase font-bold">${trx.metode_pembayaran}</span>
                </div>
                ${trx.metode_pembayaran === 'cash' ? `
                <div class="flex justify-between text-slate-600">
                    <span>Bayar:</span>
                    <span>${formatRupiah(trx.jumlah_bayar)}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Kembali:</span>
                    <span>${formatRupiah(trx.kembalian)}</span>
                </div>
                ` : ''}
            `;

            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
            lastCreatedTransaction = null;
            // Reload page to refresh stock count after sale
            window.location.reload();
        }

        function printThermalReceipt() {
            if (lastCreatedTransaction && lastCreatedTransaction.id) {
                const strukUrl = "{{ url('kasir/struk') }}/" + lastCreatedTransaction.id;
                const win = window.open(strukUrl, '_blank', 'width=350,height=600');
                if (win) {
                    win.focus();
                } else {
                    window.print();
                }
            } else {
                window.print();
            }
        }

        // 11. KEYBOARD SHORTCUTS
        document.addEventListener('keydown', function(e) {
            // F8 or Enter on non-inputs to open payment
            if (e.key === 'F8') {
                e.preventDefault();
                if (cart.length > 0) {
                    openPaymentModal();
                }
            }
            if (e.key === 'Escape') {
                closePaymentModal();
                closeReceiptModal();
            }
        });

        // Initialize cart on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderCart();
        });
    </script>
</x-app-layout>
