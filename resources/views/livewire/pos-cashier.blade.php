<div class="h-[calc(100vh-6.5rem)] flex flex-col lg:flex-row gap-5">

    <!-- BAGIAN KIRI: Katalog Menu & Pencarian (Lebar 2/3) -->
    <div class="flex-1 flex flex-col min-w-0 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs overflow-hidden">
        
        <!-- Search & Filter Kategori -->
        <div class="space-y-3 mb-4 flex-shrink-0">
            <div class="relative">
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Ketik untuk mencari menu kopi, minuman, makanan..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none transition-all"
                >
            </div>

            <!-- Tombol Kategori -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button 
                    type="button" 
                    wire:click="$set('selectedCategory', 'all')"
                    class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $selectedCategory === 'all' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button 
                        type="button" 
                        wire:click="$set('selectedCategory', '{{ $cat }}')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $selectedCategory === $cat ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Grid Menu Produk -->
        <div class="flex-1 overflow-y-auto pr-1">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
                @forelse($menuList as $m)
                    <button 
                        type="button" 
                        wire:click="addToCart({{ $m->id }})" 
                        class="group p-4 rounded-xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-400 transition-all text-left flex flex-col justify-between shadow-xs cursor-pointer active:scale-98"
                    >
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white text-slate-500 border border-slate-200 mb-2">
                                {{ $m->kategori }}
                            </span>
                            <h4 class="font-bold text-slate-900 text-sm group-hover:text-emerald-700 transition-colors line-clamp-2 leading-snug">
                                {{ $m->nama_menu }}
                            </h4>
                        </div>

                        <div class="mt-4 pt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="text-sm font-extrabold text-emerald-600">
                                Rp {{ number_format($m->harga_jual, 0, ',', '.') }}
                            </span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs group-hover:bg-emerald-700">
                                +
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-400">
                        <div class="text-sm font-semibold text-slate-600">Menu tidak ditemukan</div>
                        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- BAGIAN KANAN: Keranjang & Pembayaran (Lebar 1/3) -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl border border-slate-200 p-5 shadow-xs overflow-hidden flex-shrink-0">
        
        <!-- Header Keranjang -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                    {{ $this->totalItems }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Keranjang Pesanan</h3>
                </div>
            </div>

            @if(count($cart) > 0)
                <button 
                    type="button" 
                    wire:click="clearCart" 
                    wire:confirm="Kosongkan seluruh keranjang?"
                    class="text-xs text-rose-600 hover:underline font-semibold"
                >
                    Reset
                </button>
            @endif
        </div>

        <!-- Daftar Item Keranjang (Scrollable) -->
        <div class="flex-1 overflow-y-auto py-3 space-y-2 pr-1">
            @forelse($cart as $index => $item)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2.5">
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-slate-900 text-xs truncate">{{ $item['nama'] }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            @ Rp {{ number_format($item['harga'], 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Tombol Qty - & + -->
                    <div class="flex items-center gap-1.5">
                        <button 
                            type="button" 
                            wire:click="updateQty({{ $index }}, -1)"
                            class="w-6 h-6 rounded bg-white hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs transition-colors"
                        >
                            -
                        </button>
                        <span class="text-xs font-bold text-slate-900 w-5 text-center">
                            {{ $item['qty'] }}
                        </span>
                        <button 
                            type="button" 
                            wire:click="updateQty({{ $index }}, 1)"
                            class="w-6 h-6 rounded bg-white hover:bg-slate-200 border border-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs transition-colors"
                        >
                            +
                        </button>
                    </div>

                    <!-- Subtotal & Hapus -->
                    <div class="text-right">
                        <div class="font-bold text-xs text-slate-900">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                        <button 
                            type="button" 
                            wire:click="removeFromCart({{ $index }})" 
                            class="text-[10px] text-rose-500 hover:underline mt-0.5"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 py-12">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-2xl mb-2">
                        🛒
                    </div>
                    <div class="text-xs font-semibold text-slate-600">Keranjang Masih Kosong</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih menu di sebelah kiri untuk memesan.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer Ringkasan & Tombol Bayar -->
        <div class="pt-3 border-t border-slate-200 flex-shrink-0 space-y-3">
            <div class="flex justify-between items-center text-xs text-slate-600">
                <span>Total Item:</span>
                <span class="font-bold text-slate-800">{{ $this->totalItems }} Cup/Pcs</span>
            </div>

            <div class="flex justify-between items-center text-base font-bold text-slate-900 pt-1 border-t border-dashed border-slate-200">
                <span>Grand Total:</span>
                <span class="text-xl text-emerald-600 font-black">
                    Rp {{ number_format($this->totalHarga, 0, ',', '.') }}
                </span>
            </div>

            <button 
                type="button" 
                wire:click="openCheckout" 
                @disabled(empty($cart))
                class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-extrabold text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>BAYAR SEKARANG (F8)</span>
            </button>
        </div>
    </div>

    <!-- MODAL CHECKOUT PEMBAYARAN -->
    @if($showCheckoutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="$set('showCheckoutModal', false)"></div>

            <div class="relative bg-white border border-slate-200 rounded-2xl max-w-md w-full p-6 shadow-xl z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pembayaran Pesanan</h3>
                        <p class="text-xs text-slate-500">Pilih metode & nominal pembayaran</p>
                    </div>
                    <button wire:click="$set('showCheckoutModal', false)" class="text-slate-400 hover:text-slate-700 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Total Tagihan -->
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-center mb-4">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Tagihan</span>
                    <div class="text-2xl font-black text-emerald-700 mt-0.5">
                        Rp {{ number_format($this->totalHarga, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Pilihan Metode Pembayaran -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                        <button 
                            type="button" 
                            wire:click="$set('metodePembayaran', 'cash')"
                            class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all {{ $metodePembayaran === 'cash' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            <span>💵 Tunai (Cash)</span>
                        </button>

                        <button 
                            type="button" 
                            wire:click="$set('metodePembayaran', 'debit_qris')"
                            class="p-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition-all {{ $metodePembayaran === 'debit_qris' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}"
                        >
                            <span>📱 Debit / QRIS</span>
                        </button>
                    </div>

                    @if($metodePembayaran === 'cash')
                        <!-- Input Nominal Uang Diterima -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Uang Diterima (Rp)</label>
                            <input 
                                type="number" 
                                wire:model.live="jumlahBayar" 
                                autofocus
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-lg font-black focus:bg-white focus:border-emerald-600 outline-none"
                            >

                            <!-- Tombol Uang Pas & Cepat -->
                            <div class="grid grid-cols-4 gap-1.5 mt-2">
                                <button type="button" wire:click="setExactCash" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200">
                                    Uang Pas
                                </button>
                                <button type="button" wire:click="setQuickCash(20000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200">
                                    20.000
                                </button>
                                <button type="button" wire:click="setQuickCash(50000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200">
                                    50.000
                                </button>
                                <button type="button" wire:click="setQuickCash(100000)" class="py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-[11px] font-bold text-slate-800 border border-slate-200">
                                    100.000
                                </button>
                            </div>
                        </div>

                        <!-- Tampilan Kembalian -->
                        <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-600 uppercase">Kembalian</span>
                            <span class="text-lg font-black text-slate-900">
                                Rp {{ number_format($kembalian, 0, ',', '.') }}
                            </span>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 text-xs font-medium text-center">
                            Pembayaran non-tunai melalui EDC / Scan QRIS. Total pembayaran langsung sesuai tagihan.
                        </div>
                    @endif

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pesanan (Opsional)</label>
                        <input 
                            type="text" 
                            wire:model="catatan" 
                            placeholder="Contoh: Less ice, Meja 3" 
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                        >
                    </div>
                </div>

                <!-- Tombol Konfirmasi -->
                <div class="mt-6 flex items-center justify-end gap-2">
                    <button 
                        type="button" 
                        wire:click="$set('showCheckoutModal', false)" 
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        wire:click="processCheckout" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm cursor-pointer"
                    >
                        Konfirmasi & Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL STRUK CETAK THERMAL -->
    @if($showReceiptModal && $lastTransaksi)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" wire:click="closeReceipt"></div>

            <div class="relative bg-white border border-slate-200 rounded-2xl max-w-xs w-full p-5 shadow-xl z-10">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 mb-3">
                    <span class="text-xs font-bold text-emerald-700">✓ Transaksi Sukses</span>
                    <button wire:click="closeReceipt" class="text-slate-400 hover:text-slate-700 text-xs font-bold">
                        Tutup
                    </button>
                </div>

                <!-- Struk Thermal 58mm -->
                <div id="printable-receipt" class="bg-white text-slate-900 p-3 rounded-lg font-mono text-xs border border-slate-200">
                    <div class="text-center border-b border-dashed border-slate-300 pb-2 mb-2">
                        <div class="text-sm font-bold">KASIR CLOVER</div>
                        <div class="text-[10px] text-slate-500">Coffee & Roastery</div>
                    </div>

                    <div class="space-y-0.5 text-[10px] border-b border-dashed border-slate-300 pb-2 mb-2">
                        <div class="flex justify-between">
                            <span>No:</span>
                            <span class="font-bold">{{ $lastTransaksi->kode_transaksi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tgl:</span>
                            <span>{{ $lastTransaksi->tanggal_transaksi->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kasir:</span>
                            <span>{{ $lastTransaksi->kasir->name ?? 'Kasir' }}</span>
                        </div>
                        @if($lastTransaksi->catatan)
                            <div class="flex justify-between">
                                <span>Note:</span>
                                <span>{{ $lastTransaksi->catatan }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1 border-b border-dashed border-slate-300 pb-2 mb-2">
                        @foreach($lastTransaksi->details as $d)
                            <div>
                                <div class="font-bold">{{ $d->nama_menu_snapshot }}</div>
                                <div class="flex justify-between text-[10px] text-slate-600">
                                    <span>{{ $d->qty }}x @ {{ number_format($d->harga_satuan_snapshot, 0, ',', '.') }}</span>
                                    <span>{{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-0.5 text-[10px]">
                        <div class="flex justify-between font-bold text-xs pt-1">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($lastTransaksi->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Metode:</span>
                            <span class="uppercase font-bold">{{ $lastTransaksi->metode_pembayaran }}</span>
                        </div>
                        @if($lastTransaksi->metode_pembayaran === 'cash')
                            <div class="flex justify-between text-slate-600">
                                <span>Bayar:</span>
                                <span>{{ number_format($lastTransaksi->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-bold">
                                <span>Kembali:</span>
                                <span>{{ number_format($lastTransaksi->kembalian, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="text-center pt-3 mt-2 border-t border-dashed border-slate-300 text-[9px] text-slate-400">
                        <p>Terima kasih atas pesanan Anda!</p>
                    </div>
                </div>

                <!-- Tombol Aksi Struk -->
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center justify-center gap-1 cursor-pointer"
                    >
                        <span>🖨️ Cetak Struk</span>
                    </button>
                    <button 
                        type="button" 
                        wire:click="closeReceipt" 
                        class="py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
