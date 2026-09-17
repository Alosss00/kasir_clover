<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">Riwayat Pembelian & Audit Stok</h2>
                <p class="text-xs text-slate-400 mt-1">Audit trail histori restock untuk bahan: <strong class="text-emerald-400 font-bold">{{ $bahanBaku->nama_bahan }}</strong></p>
            </div>
            <a href="{{ route('bahan-baku.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-sm font-semibold border border-slate-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Bahan Baku</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info Card Bahan Baku -->
        <div class="glass-panel p-6 rounded-3xl grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Nama Bahan</span>
                <div class="text-lg font-extrabold text-white mt-0.5">{{ $bahanBaku->nama_bahan }}</div>
            </div>
            <div>
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Stok Tersisa Saat Ini</span>
                <div class="text-lg font-extrabold text-emerald-400 mt-0.5">{{ number_format($bahanBaku->stok_total, 2, ',', '.') }} {{ $bahanBaku->satuan }}</div>
            </div>
            <div>
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Harga Rata-Rata WAC</span>
                <div class="text-lg font-extrabold text-teal-400 mt-0.5">Rp {{ number_format($bahanBaku->harga_per_satuan, 2, ',', '.') }} / {{ $bahanBaku->satuan }}</div>
            </div>
            <div>
                <span class="text-xs text-slate-400 uppercase font-bold tracking-wider">Total Nilai Akumulasi Beli</span>
                <div class="text-lg font-extrabold text-amber-400 mt-0.5">Rp {{ number_format($bahanBaku->total_harga_beli, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Tabel Riwayat Pembelian -->
        <div class="glass-panel rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/90 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-4 px-6">Tanggal Masuk</th>
                            <th class="py-4 px-4 text-right">Jumlah Ditambahkan</th>
                            <th class="py-4 px-4 text-right">Harga Beli Total</th>
                            <th class="py-4 px-4 text-right">Harga per Satuan Saat Beli</th>
                            <th class="py-4 px-6">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($histori as $h)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-4 px-6 font-semibold text-white">
                                    {{ $h->tanggal ? $h->tanggal->translatedFormat('d F Y') : $h->created_at->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-emerald-400">
                                    +{{ number_format($h->jumlah_ditambahkan, 2, ',', '.') }} {{ $bahanBaku->satuan }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-slate-100">
                                    Rp {{ number_format($h->harga_beli, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right text-slate-300">
                                    Rp {{ $h->jumlah_ditambahkan > 0 ? number_format($h->harga_beli / $h->jumlah_ditambahkan, 2, ',', '.') : '0' }} / {{ $bahanBaku->satuan }}
                                </td>
                                <td class="py-4 px-6 text-slate-400">
                                    {{ $h->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400">
                                    Belum ada riwayat pembelian untuk bahan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($histori->hasPages())
                <div class="p-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $histori->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
