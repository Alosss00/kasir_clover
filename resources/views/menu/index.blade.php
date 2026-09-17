<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Daftar Menu & Resep HPP</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola menu, komposisi resep, dan keuntungan per cup</p>
            </div>
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('menu.export-excel') }}" 
                    class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs flex items-center gap-1.5"
                >
                    <span>📊 Export Excel</span>
                </a>
                <a 
                    href="{{ route('menu.create') }}" 
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all"
                >
                    <span>+ Buat Menu Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <!-- 3 Kartu Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Menu</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalMenu }} <span class="text-xs font-normal text-slate-500">Menu</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                    📋
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu Aktif (Siap Jual)</p>
                    <h3 class="text-2xl font-bold text-emerald-700 mt-1">{{ $totalAktif }} <span class="text-xs font-normal text-slate-500">Menu</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    ✓
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-Rata Margin</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ round($avgMargin, 1) }}%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                    📈
                </div>
            </div>
        </div>

        <!-- Filter Kategori & Pencarian -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('menu.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari menu kopi atau makanan..." 
                        class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs focus:bg-white focus:border-emerald-600 outline-none"
                    >
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="kategori" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs focus:bg-white focus:border-emerald-600 outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                        Cari
                    </button>

                    @if(request('search') || request('kategori'))
                        <a href="{{ route('menu.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Menu & Kalkulasi HPP -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-5">Nama Menu</th>
                            <th class="py-3 px-4">Komposisi Resep</th>
                            <th class="py-3 px-4 text-right">Modal/HPP</th>
                            <th class="py-3 px-4 text-right">Harga Jual</th>
                            <th class="py-3 px-4 text-right">Untung/Cup</th>
                            <th class="py-3 px-3 text-center">Margin</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($menus as $m)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $m->nama_menu }}</div>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        {{ $m->kategori }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="space-y-0.5 text-[11px]">
                                        @forelse($m->resep as $r)
                                            <div class="flex items-center justify-between text-slate-600">
                                                <span>• {{ $r->bahanBaku->nama_bahan ?? 'Bahan' }}</span>
                                                <span class="font-medium text-slate-800 ml-2">{{ number_format($r->jumlah_pemakaian, 0, ',', '.') }}{{ $r->bahanBaku->satuan ?? '' }}</span>
                                            </div>
                                        @empty
                                            <span class="text-slate-400 italic">Tanpa resep</span>
                                        @endforelse
                                        @if($m->biaya_lain > 0)
                                            <div class="text-slate-500 text-[10px] pt-0.5">
                                                + Cup/Kemasan: Rp {{ number_format($m->biaya_lain, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                    Rp {{ number_format($m->cost_per_cup, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                    Rp {{ number_format($m->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-emerald-700">
                                    Rp {{ number_format($m->keuntungan_per_cup, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->margin_persen >= 40 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $m->margin_persen }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <form method="POST" action="{{ route('menu.toggle-active', $m) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition-all {{ $m->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}"
                                            title="Klik untuk ganti status"
                                        >
                                            {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a 
                                            href="{{ route('menu.edit', $m) }}" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-all"
                                            title="Edit Menu & Resep"
                                        >
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('menu.destroy', $m) }}" onsubmit="return confirm('Hapus menu {{ $m->nama_menu }}?')">
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
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    Belum ada menu yang dibuat. Klik tombol "+ Buat Menu Baru" di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($menus->hasPages())
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
