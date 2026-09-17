<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Laporan Penjualan & Omzet</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: <strong class="text-slate-800">{{ $startDate->translatedFormat('d F Y') }}</strong> s/d <strong class="text-slate-800">{{ $endDate->translatedFormat('d F Y') }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('laporan.penjualan.excel', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" 
                    class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all"
                >
                    <span>📊 Download Excel</span>
                </a>

                <a 
                    href="{{ route('laporan.penjualan.pdf', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" 
                    class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs flex items-center gap-1.5 transition-all"
                >
                    <span>📄 Download PDF</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5" x-data="{ currentTab: '{{ $filterType }}' }">

        <!-- Tab Filter Sederhana -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-slate-100">
                <a 
                    href="{{ route('laporan.penjualan', ['filter_type' => 'harian']) }}" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'harian' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    Hari Ini
                </a>
                <a 
                    href="{{ route('laporan.penjualan', ['filter_type' => 'mingguan']) }}" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'mingguan' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    7 Hari Terakhir
                </a>
                <a 
                    href="{{ route('laporan.penjualan', ['filter_type' => 'bulanan']) }}" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'bulanan' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    Bulan Ini
                </a>
                <a 
                    href="{{ route('laporan.penjualan', ['filter_type' => 'tahunan']) }}" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'tahunan' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    Tahun Ini
                </a>
                <button 
                    type="button" 
                    @click="currentTab = 'custom'" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterType === 'custom' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    Rentang Tanggal Custom
                </button>
            </div>

            <!-- Form Rentang Tanggal Custom -->
            <form method="GET" action="{{ route('laporan.penjualan') }}" class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                <input type="hidden" name="filter_type" value="custom">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-500">Dari:</span>
                    <input type="date" name="dari" value="{{ $startDate->toDateString() }}" class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 text-xs focus:border-emerald-600 outline-none">
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-500">Sampai:</span>
                    <input type="date" name="sampai" value="{{ $endDate->toDateString() }}" class="px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-slate-800 text-xs focus:border-emerald-600 outline-none">
                </div>
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all">
                    Terapkan
                </button>
            </form>
        </div>

        <!-- 4 Kartu Ringkasan Keuangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Omzet</span>
                <h3 class="text-2xl font-bold text-emerald-700 mt-1">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalTransaksi }} pesanan</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</span>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">
                    {{ $totalTransaksi }} <span class="text-xs font-normal text-slate-500">Order</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Rata-rata: Rp {{ $totalTransaksi > 0 ? number_format($totalOmzet / $totalTransaksi, 0, ',', '.') : 0 }}</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tunai (Cash)</span>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">
                    Rp {{ number_format($cashOmzet, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $cashCount }} transaksi</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Debit / QRIS</span>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">
                    Rp {{ number_format($debitOmzet, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $debitCount }} transaksi</p>
            </div>
        </div>

        <!-- Top 5 Menu Laris -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center gap-1.5">
                    <span>🏆</span>
                    <span>Menu Paling Laris (Top 5)</span>
                </h3>

                <div class="space-y-2">
                    @forelse($topMenus as $index => $top)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-200 font-bold text-[10px] text-slate-700 flex items-center justify-center">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $top->nama_menu_snapshot }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $top->total_qty }} cup terjual</div>
                                </div>
                            </div>
                            <div class="font-bold text-slate-800">
                                Rp {{ number_format($top->total_omzet, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            Belum ada penjualan di periode ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tren Harian -->
            <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center gap-1.5">
                    <span>📈</span>
                    <span>Tren Penjualan Harian</span>
                </h3>

                <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                    @forelse($trendHarian as $t)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                            <div class="font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($t->date)->translatedFormat('l, d F Y') }}
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-slate-400">{{ $t->count }} Order</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($t->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-xs text-slate-400">
                            Belum ada tren penjualan untuk ditampilkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Transaksi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Riwayat Transaksi Penjualan</h3>
                <span class="text-xs text-slate-500">{{ $transaksiList->total() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-5">Kode & Waktu</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Menu Dipesan</th>
                            <th class="py-3 px-3 text-center">Metode</th>
                            <th class="py-3 px-5 text-right">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiList as $trx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-xs">{{ $trx->kode_transaksi }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $trx->tanggal_transaksi->translatedFormat('d M Y, H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-emerald-800 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <span>👤</span>
                                        <span>{{ $trx->nama_customer ?? 'Pelanggan Umum' }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $trx->kasir->name ?? 'Kasir' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="space-y-0.5 max-w-sm">
                                        @foreach($trx->details as $d)
                                            <div class="flex items-center justify-between text-slate-600">
                                                <span>{{ $d->nama_menu_snapshot }} ({{ $d->qty }}x)</span>
                                                <span class="font-semibold text-slate-800 ml-2">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $trx->metode_pembayaran === 'cash' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $trx->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right font-bold text-emerald-700 text-sm">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksiList->hasPages())
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    {{ $transaksiList->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
