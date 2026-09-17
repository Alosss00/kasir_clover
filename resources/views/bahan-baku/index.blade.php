<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Manajemen Bahan Baku & Stok</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola stok bahan dengan akumulasi harga rata-rata otomatis (WAC)</p>
            </div>
            <div>
                <button 
                    @click="$dispatch('open-modal', 'modal-tambah-bahan')"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah / Restock Bahan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5" x-data="{ helperMode: false, kemasanQty: 1, kemasanIsi: 1000, kemasanHarga: 23000 }">

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
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
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
                                                class="px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-xs"
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

        <!-- Modal Tambah / Restock Sederhana -->
        <div 
            x-data="{ show: false }" 
            x-on:open-modal.window="if ($event.detail === 'modal-tambah-bahan') show = true"
            x-on:close-modal.window="show = false"
            x-show="show" 
            x-cloak 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" @click="show = false"></div>

            <div class="relative bg-white border border-slate-200 rounded-2xl max-w-lg w-full p-6 shadow-xl z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tambah / Restock Bahan Baku</h3>
                        <p class="text-xs text-slate-500">Stok & harga rata-rata dihitung otomatis</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <form method="POST" action="{{ route('bahan-baku.store') }}" class="space-y-3.5 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Bahan Baku</label>
                        <input 
                            type="text" 
                            name="nama_bahan" 
                            required 
                            placeholder="Contoh: Susu UHT Full Cream, Biji Kopi Blend" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Satuan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="radio" name="satuan" value="gr" class="text-emerald-600">
                                <span>Gram (gr) - Bubuk/Biji</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="radio" name="satuan" value="ml" checked class="text-emerald-600">
                                <span>Mililiter (ml) - Cairan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kalkulator Pembelian Kemasan Opsional -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-700">🛠️ Bantuan Hitung Kemasan (Karton/Box)</span>
                            <button type="button" @click="helperMode = !helperMode" class="text-emerald-700 hover:underline font-bold">
                                <span x-text="helperMode ? 'Tutup' : 'Buka Kalkulator'"></span>
                            </button>
                        </div>

                        <div x-show="helperMode" x-cloak class="space-y-2 pt-2 border-t border-slate-200 mt-2">
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="text-slate-500 block">Jml Dus/Box</label>
                                    <input type="number" x-model.number="kemasanQty" min="1" class="w-full p-1.5 rounded-lg bg-white border border-slate-300">
                                </div>
                                <div>
                                    <label class="text-slate-500 block">Isi per Dus</label>
                                    <input type="number" x-model.number="kemasanIsi" min="1" class="w-full p-1.5 rounded-lg bg-white border border-slate-300">
                                </div>
                                <div>
                                    <label class="text-slate-500 block">Harga/Dus (Rp)</label>
                                    <input type="number" x-model.number="kemasanHarga" min="0" class="w-full p-1.5 rounded-lg bg-white border border-slate-300">
                                </div>
                            </div>
                            <button 
                                type="button" 
                                @click="
                                    $el.form.jumlah.value = kemasanQty * kemasanIsi;
                                    $el.form.harga_beli.value = kemasanQty * kemasanHarga;
                                "
                                class="w-full py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs"
                            >
                                Salin ke Form (Total: <span x-text="(kemasanQty * kemasanIsi).toLocaleString('id-ID')"></span> ml/gr, Rp <span x-text="(kemasanQty * kemasanHarga).toLocaleString('id-ID')"></span>)
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jumlah Stok (gr/ml)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="jumlah" 
                                required 
                                placeholder="Contoh: 12000" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Total Harga Beli (Rp)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="harga_beli" 
                                required 
                                placeholder="Contoh: 276000" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Batas Minimum Peringatan</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                name="stok_minimum" 
                                value="500" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal</label>
                            <input 
                                type="date" 
                                name="tanggal" 
                                value="{{ date('Y-m-d') }}" 
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 outline-none"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan (Opsional)</label>
                        <input 
                            type="text" 
                            name="keterangan" 
                            placeholder="Contoh: Restock Supplier A" 
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                        >
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-200">
                        <button type="button" @click="show = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-xs">
                            Simpan Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
