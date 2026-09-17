<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Manajemen Bahan Baku & Stok</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola stok bahan dengan akumulasi harga rata-rata otomatis (WAC)</p>
            </div>
            <div>
                <button 
                    @click="openRestockModal()"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah / Restock Bahan</span>
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $bahanJson = $allBahanList->map(function($b) {
            return [
                'id' => $b->id,
                'nama_bahan' => $b->nama_bahan,
                'satuan' => $b->satuan,
                'stok_total' => (float)$b->stok_total,
                'harga_per_satuan' => (float)$b->harga_per_satuan,
                'total_harga_beli' => (float)$b->total_harga_beli,
                'stok_minimum' => (float)$b->stok_minimum,
                'is_menipis' => $b->isStokMenipis(),
            ];
        });
    @endphp

    <div 
        class="space-y-5" 
        x-data="{
            showModal: false,
            tabMode: 'existing', // 'existing' or 'new'
            bahanList: {{ Js::from($bahanJson) }},
            selectedBahanId: '{{ $allBahanList->first()?->id ?? '' }}',
            
            // Form values
            jumlah: '',
            hargaBeli: '',
            stokMinimum: 500,
            tanggal: '{{ date('Y-m-d') }}',
            keterangan: 'Restock Stok Bahan',
            
            // New ingredient values
            newNamaBahan: '',
            newSatuan: 'ml',
            newStokMinimum: 500,

            // Packaging helper
            helperMode: false,
            kemasanQty: 1,
            kemasanIsi: 1000,
            kemasanHarga: 23000,

            get selectedBahan() {
                return this.bahanList.find(b => b.id == this.selectedBahanId) || null;
            },

            get newEstimatedStock() {
                if (!this.selectedBahan) return 0;
                return (Number(this.selectedBahan.stok_total) || 0) + (Number(this.jumlah) || 0);
            },

            get newEstimatedWac() {
                if (!this.selectedBahan) return 0;
                const totalQty = this.newEstimatedStock;
                const totalBeli = (Number(this.selectedBahan.total_harga_beli) || 0) + (Number(this.hargaBeli) || 0);
                return totalQty > 0 ? (totalBeli / totalQty) : 0;
            },

            openRestockModal(bahanId = null) {
                if (bahanId) {
                    this.selectedBahanId = bahanId;
                    this.tabMode = 'existing';
                } else {
                    if (!this.selectedBahanId && this.bahanList.length > 0) {
                        this.selectedBahanId = this.bahanList[0].id;
                    }
                }
                this.jumlah = '';
                this.hargaBeli = '';
                this.helperMode = false;
                this.showModal = true;
            },

            applyPackagingHelper() {
                this.jumlah = this.kemasanQty * this.kemasanIsi;
                this.hargaBeli = this.kemasanQty * this.kemasanHarga;
            }
        }"
        x-on:open-modal.window="if ($event.detail === 'modal-tambah-bahan') openRestockModal()"
    >

        <!-- 3 Kartu Ringkasan Stok -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Jenis Bahan</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalItem }} <span class="text-xs font-normal text-slate-500">Bahan</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                    📦
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Nilai Stok</p>
                    <h3 class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($totalNilaiInventaris, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    💰
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peringatan Stok Menipis</p>
                    <h3 class="text-2xl font-bold {{ $totalStokMenipis > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-1">
                        {{ $totalStokMenipis }} <span class="text-xs font-normal text-slate-500">Bahan</span>
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $totalStokMenipis > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center font-bold text-lg">
                    ⚠️
                </div>
            </div>
        </div>

        <!-- Filter & Pencarian -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('bahan-baku.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama bahan baku (misal: susu, kopi, gula)..." 
                        class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                    >
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-slate-100">
                        <input type="checkbox" name="stok_menipis" value="1" {{ request('stok_menipis') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-emerald-600">
                        <span>Hanya Stok Menipis</span>
                    </label>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                        Cari
                    </button>

                    @if(request('search') || request('stok_menipis'))
                        <a href="{{ route('bahan-baku.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Bahan Baku -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-5">Nama Bahan</th>
                            <th class="py-3 px-3 text-center">Satuan</th>
                            <th class="py-3 px-4 text-right">Stok Sisa</th>
                            <th class="py-3 px-4 text-right">Harga Rata-Rata (WAC)</th>
                            <th class="py-3 px-4 text-right">Total Nilai</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bahanBaku as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $item->nama_bahan }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Dipakai pada {{ $item->resep()->count() }} resep menu
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $item->satuan }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-bold text-sm {{ $item->isStokMenipis() ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ number_format($item->stok_total, 2, ',', '.') }}
                                    </span>
                                    <span class="text-slate-500 ml-0.5">{{ $item->satuan }}</span>
                                    @if($item->isStokMenipis())
                                        <div class="text-[10px] text-rose-500 font-semibold">Min: {{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $item->satuan }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="font-bold text-emerald-700">
                                        Rp {{ number_format($item->harga_per_satuan, 2, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">per {{ $item->satuan }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                    Rp {{ number_format($item->total_nilai_stok, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($item->isStokMenipis())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">
                                            <span>⚠️</span> Menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span>✓</span> Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Cepat Restock Baris Ini -->
                                        <button 
                                            type="button" 
                                            @click="openRestockModal({{ $item->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs transition-all cursor-pointer flex items-center gap-1"
                                            title="Restock bahan ini"
                                        >
                                            <span>+ Restock</span>
                                        </button>

                                        <a 
                                            href="{{ route('bahan-baku.histori', $item) }}" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-all"
                                            title="Histori Pembelian"
                                        >
                                            Histori
                                        </a>

                                        <form method="POST" action="{{ route('bahan-baku.destroy', $item) }}" onsubmit="return confirm('Hapus bahan {{ $item->nama_bahan }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs cursor-pointer"
                                                title="Hapus"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Belum ada data bahan baku. Klik tombol "+ Tambah / Restock Bahan" di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bahanBaku->hasPages())
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    {{ $bahanBaku->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL TAMBAH / RESTOCK BAHAN BAKU -->
        <div 
            x-show="showModal" 
            x-cloak 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="showModal = false"></div>

            <div class="relative bg-white border border-slate-200 rounded-2xl max-w-xl w-full p-6 shadow-2xl z-10">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Kelola Stok Bahan Baku</h3>
                        <p class="text-xs text-slate-500">Pilih restock bahan yang sudah ada atau buat bahan baru</p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-lg font-bold">✕</button>
                </div>

                <!-- 2 TAB PILIHAN (RESTOCK EXISTING vs BAHAN BARU) -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl mb-5">
                    <button 
                        type="button" 
                        @click="tabMode = 'existing'" 
                        class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                        :class="tabMode === 'existing' ? 'bg-white text-emerald-700 shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>🔄 Restock Bahan yang Ada</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-700 font-bold" x-text="bahanList.length"></span>
                    </button>

                    <button 
                        type="button" 
                        @click="tabMode = 'new'" 
                        class="py-2 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                        :class="tabMode === 'new' ? 'bg-white text-emerald-700 shadow-xs border border-slate-200/80' : 'text-slate-600 hover:text-slate-900'"
                    >
                        <span>✨ Tambah Bahan Baru</span>
                    </button>
                </div>

                <!-- FORM UTAMA -->
                <form method="POST" action="{{ route('bahan-baku.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="mode" :value="tabMode">

                    <!-- ============================================== -->
                    <!-- TAB 1: RESTOCK KE BAHAN YANG SUDAH ADA -->
                    <!-- ============================================== -->
                    <div x-show="tabMode === 'existing'" class="space-y-3.5">
                        
                        <!-- Pilihan Dropdown Bahan (Diurutkan Stok Menipis Paling Atas) -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                <span>Pilih Bahan Baku yang Ingin Di-restock:</span>
                                <span class="text-[10px] text-slate-400 font-normal">Diutamakan stok menipis di atas</span>
                            </label>
                            
                            <select 
                                name="bahan_baku_id" 
                                x-model="selectedBahanId" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-bold focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none cursor-pointer"
                                :required="tabMode === 'existing'"
                            >
                                <template x-for="item in bahanList" :key="item.id">
                                    <option 
                                        :value="item.id" 
                                        x-text="(item.is_menipis ? '⚠️ [MENIPIS] ' : '🟢 [AMAN] ') + item.nama_bahan + ' (Sisa: ' + item.stok_total.toLocaleString('id-ID') + ' ' + item.satuan + ')'"
                                    ></option>
                                </template>
                            </select>
                        </div>

                        <!-- Kartu Informasi Detail Bahan Terpilih -->
                        <template x-if="selectedBahan">
                            <div class="p-3.5 rounded-xl border transition-all" :class="selectedBahan.is_menipis ? 'bg-rose-50/70 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-800'">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-bold text-sm" x-text="selectedBahan.nama_bahan"></div>
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase"
                                        :class="selectedBahan.is_menipis ? 'bg-rose-100 text-rose-700 border border-rose-300 animate-pulse' : 'bg-emerald-100 text-emerald-800 border border-emerald-300'"
                                        x-text="selectedBahan.is_menipis ? '⚠️ Perlu Restock' : '✓ Stok Cukup'"
                                    ></span>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-2 text-center pt-2 border-t" :class="selectedBahan.is_menipis ? 'border-rose-200/80' : 'border-slate-200'">
                                    <div>
                                        <div class="text-[10px] text-slate-500">Stok Saat Ini</div>
                                        <div class="font-extrabold text-xs" x-text="selectedBahan.stok_total.toLocaleString('id-ID') + ' ' + selectedBahan.satuan"></div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-500">Batas Min. Aman</div>
                                        <div class="font-extrabold text-xs" x-text="selectedBahan.stok_minimum.toLocaleString('id-ID') + ' ' + selectedBahan.satuan"></div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-500">Harga WAC Saat Ini</div>
                                        <div class="font-extrabold text-xs text-emerald-700" x-text="'Rp ' + Math.round(selectedBahan.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedBahan.satuan"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    <!-- ============================================== -->
                    <!-- TAB 2: TAMBAH BAHAN BAKU BARU -->
                    <!-- ============================================== -->
                    <div x-show="tabMode === 'new'" class="space-y-3.5">
                        
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Bahan Baku Baru</label>
                            <input 
                                type="text" 
                                name="nama_bahan" 
                                x-model="newNamaBahan"
                                placeholder="Contoh: Sirup Caramel Premium, Bubuk Red Velvet" 
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-emerald-600 outline-none font-medium"
                                :required="tabMode === 'new'"
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Satuan</label>
                                <select 
                                    name="satuan" 
                                    x-model="newSatuan"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-semibold focus:bg-white focus:border-emerald-600 outline-none"
                                >
                                    <option value="ml">Mililiter (ml) - Cairan / Susu / Sirup</option>
                                    <option value="gr">Gram (gr) - Bubuk / Biji Kopi</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Batas Minimum Peringatan</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="stok_minimum" 
                                    x-model="newStokMinimum"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                                >
                            </div>
                        </div>

                    </div>

                    <!-- ============================================== -->
                    <!-- KALKULATOR BANTUAN KEMASAN (DUS/KARTON) -->
                    <!-- ============================================== -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-700 flex items-center gap-1">
                                <span>🛠️</span>
                                <span>Bantuan Hitung Kemasan (Dus / Karton)</span>
                            </span>
                            <button type="button" @click="helperMode = !helperMode" class="text-emerald-700 hover:underline font-bold text-[11px] cursor-pointer">
                                <span x-text="helperMode ? 'Tutup Kalkulator' : 'Buka Kalkulator'"></span>
                            </button>
                        </div>

                        <div x-show="helperMode" x-cloak class="space-y-2.5 pt-2.5 border-t border-slate-200 mt-2">
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="text-slate-500 block text-[10px]">Jml Dus / Box</label>
                                    <input type="number" x-model.number="kemasanQty" min="1" class="w-full p-1.5 rounded-lg bg-white border border-slate-300 font-bold">
                                </div>
                                <div>
                                    <label class="text-slate-500 block text-[10px]">Isi per Dus (ml/gr)</label>
                                    <input type="number" x-model.number="kemasanIsi" min="1" class="w-full p-1.5 rounded-lg bg-white border border-slate-300 font-bold">
                                </div>
                                <div>
                                    <label class="text-slate-500 block text-[10px]">Harga per Dus (Rp)</label>
                                    <input type="number" x-model.number="kemasanHarga" min="0" class="w-full p-1.5 rounded-lg bg-white border border-slate-300 font-bold">
                                </div>
                            </div>
                            <button 
                                type="button" 
                                @click="applyPackagingHelper()"
                                class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors cursor-pointer"
                            >
                                Salin Total: <span x-text="(kemasanQty * kemasanIsi).toLocaleString('id-ID')"></span> ml/gr &bull; Rp <span x-text="(kemasanQty * kemasanHarga).toLocaleString('id-ID')"></span> ke Form
                            </button>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- INPUT JUMLAH & HARGA BELI -->
                    <!-- ============================================== -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">
                                Jumlah Stok Masuk (<span x-text="tabMode === 'existing' && selectedBahan ? selectedBahan.satuan : newSatuan"></span>)
                            </label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="jumlah" 
                                x-model="jumlah"
                                required 
                                placeholder="Contoh: 5000" 
                                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-bold focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Total Harga Beli (Rp)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="harga_beli" 
                                x-model="hargaBeli"
                                required 
                                placeholder="Contoh: 150000" 
                                class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 font-bold focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>
                    </div>

                    <!-- SIMULASI AKUMULASI WAC (TAB EXISTING) -->
                    <template x-if="tabMode === 'existing' && selectedBahan && jumlah > 0 && hargaBeli > 0">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 space-y-1">
                            <div class="font-bold text-emerald-800 text-[11px] flex items-center gap-1">
                                <span>📊</span>
                                <span>Simulasi Hasil Akumulasi WAC:</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-emerald-950">
                                <span>Estimasi Total Stok Baru:</span>
                                <span class="font-extrabold" x-text="newEstimatedStock.toLocaleString('id-ID') + ' ' + selectedBahan.satuan"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-emerald-950">
                                <span>Estimasi Harga Rata-Rata (WAC) Baru:</span>
                                <span class="font-extrabold" x-text="'Rp ' + Math.round(newEstimatedWac).toLocaleString('id-ID') + ' / ' + selectedBahan.satuan"></span>
                            </div>
                        </div>
                    </template>

                    <!-- TANGGAL & CATATAN -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Pembelian</label>
                            <input 
                                type="date" 
                                name="tanggal" 
                                x-model="tanggal"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan (Opsional)</label>
                            <input 
                                type="text" 
                                name="keterangan" 
                                x-model="keterangan"
                                placeholder="Contoh: Supplier Toko Kopi" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>
                    </div>

                    <!-- TOMBOL SUBMIT -->
                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-200">
                        <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 cursor-pointer flex items-center gap-1.5">
                            <span x-text="tabMode === 'existing' ? 'Simpan Restock Bahan' : 'Simpan Bahan Baru'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-app-layout>
