<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span>📊</span>
                    <span>Laporan Penjualan & Rekap Keuntungan</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode Terpilih: <strong class="text-slate-800">{{ $startDate->translatedFormat('d F Y') }}</strong> s/d <strong class="text-slate-800">{{ $endDate->translatedFormat('d F Y') }}</strong>
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

    <div class="space-y-6" x-data="{ currentTab: '{{ $filterType }}' }">

        <!-- ============================================================== -->
        <!-- 1. DETAIL METRIK KEUANGAN PERIODE AKTIF (UTAMA) -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TOTAL OMZET -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Omzet Penjualan</span>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">
                    Rp {{ number_format($activeMetrics['omzet'], 0, ',', '.') }}
                </h3>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1.5">
                    <span>{{ $activeMetrics['total_transaksi'] }} Order Terproses</span>
                    <span class="font-semibold text-slate-600">{{ $activeMetrics['total_cup'] }} Cup Terjual</span>
                </div>
            </div>

            <!-- TOTAL HPP / MODAL POKOK (DARI RESEP) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Modal / HPP</span>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">
                    Rp {{ number_format($activeMetrics['hpp'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Rasio Beban Pokok: 
                    <strong class="text-amber-700">{{ $activeMetrics['omzet'] > 0 ? round(($activeMetrics['hpp'] / $activeMetrics['omzet']) * 100, 1) : 0 }}%</strong>
                </p>
            </div>

            <!-- TOTAL KEUNTUNGAN BERSIH (PROFIT) -->
            <div class="bg-white p-5 rounded-2xl border border-emerald-300 bg-emerald-50/30 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Keuntungan Bersih (Profit)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                        {{ $activeMetrics['margin'] }}% Margin
                    </span>
                </div>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">
                    Rp {{ number_format($activeMetrics['profit'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-emerald-600 mt-1.5">
                    Laba kotor dari total {{ $activeMetrics['total_cup'] }} porsi terjual
                </p>
            </div>

            <!-- METODE PEMBAYARAN -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Metode Pembayaran</span>
                <div class="mt-2 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1 text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Tunai:</span>
                        </span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($activeMetrics['cash_omzet'], 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">({{ $activeMetrics['cash_count'] }})</span></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1 text-slate-600">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Debit / QRIS:</span>
                        </span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($activeMetrics['debit_omzet'], 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">({{ $activeMetrics['debit_count'] }})</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. FILTER PERIODE REKAP DETAIL -->
        <!-- ============================================================== -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-1.5 overflow-x-auto">
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
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Filter tanggal spesifik:
                </div>
            </div>

            <!-- Form Filter Spesifik Sesuai Kebutuhan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                <!-- Filter Harian Spesifik -->
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                    <input type="hidden" name="filter_type" value="harian">
                    <div class="text-xs">
                        <span class="text-slate-500 block text-[10px] font-bold uppercase">Pilih Tanggal:</span>
                        <input type="date" name="tanggal" value="{{ request('tanggal', $startDate->toDateString()) }}" class="bg-white px-2 py-1 rounded border border-slate-300 text-xs font-semibold text-slate-800">
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition-colors">
                        Tampilkan
                    </button>
                </form>

                <!-- Filter Bulanan Spesifik -->
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                    <input type="hidden" name="filter_type" value="bulanan">
                    <div class="text-xs flex items-center gap-1.5">
                        <div>
                            <span class="text-slate-500 block text-[10px] font-bold uppercase">Bulan:</span>
                            <select name="bulan" class="bg-white px-2 py-1 rounded border border-slate-300 text-xs font-semibold text-slate-800">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ (request('bulan', $startDate->format('n')) == $m) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] font-bold uppercase">Tahun:</span>
                            <select name="tahun" class="bg-white px-2 py-1 rounded border border-slate-300 text-xs font-semibold text-slate-800">
                                @for($y=date('Y'); $y>=date('Y')-4; $y--)
                                    <option value="{{ $y }}" {{ (request('tahun', $startDate->format('Y')) == $y) ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition-colors">
                        Terapkan
                    </button>
                </form>

                <!-- Filter Rentang Custom -->
                <form method="GET" action="{{ route('laporan.penjualan') }}" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2">
                    <input type="hidden" name="filter_type" value="custom">
                    <div class="text-xs flex items-center gap-1">
                        <div>
                            <span class="text-slate-500 block text-[10px] font-bold uppercase">Dari:</span>
                            <input type="date" name="dari" value="{{ $startDate->toDateString() }}" class="bg-white px-1.5 py-1 rounded border border-slate-300 text-[11px] font-semibold text-slate-800">
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] font-bold uppercase">Sampai:</span>
                            <input type="date" name="sampai" value="{{ $endDate->toDateString() }}" class="bg-white px-1.5 py-1 rounded border border-slate-300 text-[11px] font-semibold text-slate-800">
                        </div>
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 3. MATRIKS PERBANDINGAN KEUNTUNGAN (HARIAN, MINGGUAN, BULANAN, TAHUNAN) -->
        <!-- ============================================================== -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span>⚡</span>
                    <span>Ringkasan Keuntungan Multi-Periode</span>
                </h3>
                <span class="text-[11px] text-slate-400">Update Real-time Berdasarkan Resep</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- KARTU 1: HARI INI (HARIAN) -->
                <div class="bg-gradient-to-br from-emerald-50 via-white to-white p-4 rounded-2xl border border-emerald-200/80 shadow-xs hover:border-emerald-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                            <span>🌅</span>
                            <span>Hari Ini (Harian)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            {{ $summaryHarian['margin'] }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <div class="text-[11px] text-slate-500 font-medium">Keuntungan Bersih (Profit)</div>
                        <div class="text-xl font-extrabold text-emerald-700 mt-0.5">
                            Rp {{ number_format($summaryHarian['profit'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-400 block">Omzet:</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($summaryHarian['omzet'], 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Modal (HPP):</span>
                            <span class="font-semibold text-amber-700">Rp {{ number_format($summaryHarian['hpp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 text-right">
                        {{ $summaryHarian['total_transaksi'] }} Order ({{ $summaryHarian['total_cup'] }} Cup)
                    </div>
                </div>

                <!-- KARTU 2: 7 HARI TERAKHIR (MINGGUAN) -->
                <div class="bg-gradient-to-br from-blue-50 via-white to-white p-4 rounded-2xl border border-blue-200/80 shadow-xs hover:border-blue-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-blue-800">
                            <span>📅</span>
                            <span>7 Hari (Mingguan)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            {{ $summaryMingguan['margin'] }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <div class="text-[11px] text-slate-500 font-medium">Keuntungan Bersih (Profit)</div>
                        <div class="text-xl font-extrabold text-blue-700 mt-0.5">
                            Rp {{ number_format($summaryMingguan['profit'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-400 block">Omzet:</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($summaryMingguan['omzet'], 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Modal (HPP):</span>
                            <span class="font-semibold text-amber-700">Rp {{ number_format($summaryMingguan['hpp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 text-right">
                        {{ $summaryMingguan['total_transaksi'] }} Order ({{ $summaryMingguan['total_cup'] }} Cup)
                    </div>
                </div>

                <!-- KARTU 3: BULAN INI (BULANAN) -->
                <div class="bg-gradient-to-br from-indigo-50 via-white to-white p-4 rounded-2xl border border-indigo-200/80 shadow-xs hover:border-indigo-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-800">
                            <span>🗓️</span>
                            <span>Bulan Ini (Bulanan)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                            {{ $summaryBulanan['margin'] }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <div class="text-[11px] text-slate-500 font-medium">Keuntungan Bersih (Profit)</div>
                        <div class="text-xl font-extrabold text-indigo-700 mt-0.5">
                            Rp {{ number_format($summaryBulanan['profit'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-400 block">Omzet:</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($summaryBulanan['omzet'], 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Modal (HPP):</span>
                            <span class="font-semibold text-amber-700">Rp {{ number_format($summaryBulanan['hpp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 text-right">
                        {{ $summaryBulanan['total_transaksi'] }} Order ({{ $summaryBulanan['total_cup'] }} Cup)
                    </div>
                </div>

                <!-- KARTU 4: TAHUN INI (TAHUNAN) -->
                <div class="bg-gradient-to-br from-purple-50 via-white to-white p-4 rounded-2xl border border-purple-200/80 shadow-xs hover:border-purple-300 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-purple-800">
                            <span>📆</span>
                            <span>Tahun Ini (Tahunan)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                            {{ $summaryTahunan['margin'] }}% Margin
                        </span>
                    </div>

                    <div class="mt-2.5">
                        <div class="text-[11px] text-slate-500 font-medium">Keuntungan Bersih (Profit)</div>
                        <div class="text-xl font-extrabold text-purple-700 mt-0.5">
                            Rp {{ number_format($summaryTahunan['profit'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-400 block">Omzet:</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($summaryTahunan['omzet'], 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Modal (HPP):</span>
                            <span class="font-semibold text-amber-700">Rp {{ number_format($summaryTahunan['hpp'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-1 text-[10px] text-slate-400 text-right">
                        {{ $summaryTahunan['total_transaksi'] }} Order ({{ $summaryTahunan['total_cup'] }} Cup)
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. TOP MENU PROFIT MAKERS & TREN HARIAN -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            
            <!-- TOP 5 MENU PROFIT MAKERS -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span>🏆</span>
                        <span>Top 5 Menu Paling Menguntungkan</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-normal">By Porsi Terjual</span>
                </h3>

                <div class="space-y-2.5">
                    @forelse($topMenus as $index => $top)
                        @php
                            $menuMargin = (float)$top->total_omzet > 0 ? round(((float)$top->total_profit / (float)$top->total_omzet) * 100, 1) : 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-slate-200 font-bold text-[10px] text-slate-700 flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $top->nama_menu_snapshot }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $top->total_qty }} cup terjual</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-emerald-700">
                                        + Rp {{ number_format($top->total_profit, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] font-bold text-emerald-600">{{ $menuMargin }}% margin</div>
                                </div>
                            </div>

                            <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500">
                                <span>Omzet: Rp {{ number_format($top->total_omzet, 0, ',', '.') }}</span>
                                <span class="font-semibold text-amber-700">Modal: Rp {{ number_format($top->total_hpp, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            Belum ada penjualan menu di periode ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TREN PENJUALAN & KEUNTUNGAN HARIAN -->
            <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span>📈</span>
                        <span>Tren Penjualan & Keuntungan Harian</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-normal">Omzet vs Keuntungan Bersih</span>
                </h3>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse($trendHarian as $t)
                        @php
                            $dayMargin = (float)$t->total_omzet > 0 ? round(((float)$t->total_profit / (float)$t->total_omzet) * 100, 1) : 0;
                            $profitPercent = (float)$t->total_omzet > 0 ? min(100, max(0, ((float)$t->total_profit / (float)$t->total_omzet) * 100)) : 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($t->date)->translatedFormat('l, d F Y') }}
                                    <span class="text-[10px] font-normal text-slate-400 ml-1.5">({{ $t->count }} Order)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-[10px] text-slate-400">Omzet</div>
                                        <div class="font-semibold text-slate-800">Rp {{ number_format($t->total_omzet, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-slate-400">Modal (HPP)</div>
                                        <div class="font-semibold text-amber-700">Rp {{ number_format($t->total_hpp, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-emerald-600 font-bold">Keuntungan ({{ $dayMargin }}%)</div>
                                        <div class="font-bold text-emerald-700">Rp {{ number_format($t->total_profit, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar Visualisasi Rasio Profit -->
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden flex">
                                <div class="bg-amber-500 h-1.5" style="width: {{ 100 - $profitPercent }}%" title="Modal (HPP)"></div>
                                <div class="bg-emerald-500 h-1.5" style="width: {{ $profitPercent }}%" title="Keuntungan"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-xs text-slate-400">
                            Belum ada tren penjualan untuk ditampilkan pada rentang tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 5. TABEL RINCIAN RIWAYAT TRANSAKSI & PROFIT PER TRANSAKSI -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Riwayat Transaksi Penjualan & Laba</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Rincian pendapatan, modal HPP resep, dan laba per nota transaksi</p>
                </div>
                <span class="text-xs text-slate-500 font-semibold">{{ $transaksiList->total() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Kode & Waktu</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Kasir</th>
                            <th class="py-3 px-4">Menu Dipesan (Qty & Modal)</th>
                            <th class="py-3 px-3 text-center">Metode</th>
                            <th class="py-3 px-4 text-right">Omzet (Rp)</th>
                            <th class="py-3 px-3 text-right">Modal/HPP</th>
                            <th class="py-3 px-4 text-right">Keuntungan (Profit)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiList as $trx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $trx->kode_transaksi }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $trx->tanggal_transaksi->translatedFormat('d M Y, H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-3 font-bold text-emerald-800 text-xs">
                                    <span class="inline-flex items-center gap-1">
                                        <span>👤</span>
                                        <span>{{ $trx->nama_customer ?? 'Pelanggan Umum' }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-medium text-slate-700">
                                    {{ $trx->kasir->name ?? 'Kasir' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="space-y-1 max-w-sm">
                                        @foreach($trx->details as $d)
                                            <div class="flex items-center justify-between text-slate-600 bg-slate-50 px-2 py-1 rounded">
                                                <span>{{ $d->nama_menu_snapshot }} ({{ $d->qty }}x)</span>
                                                <div class="text-right">
                                                    <span class="font-semibold text-slate-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                                    <span class="text-[9px] text-amber-700 font-medium block">HPP: Rp {{ number_format($d->total_hpp, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $trx->metode_pembayaran === 'cash' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $trx->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right text-amber-700 font-semibold">
                                    Rp {{ number_format($trx->total_hpp, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="font-bold text-emerald-700 text-sm">
                                        Rp {{ number_format($trx->total_profit, 0, ',', '.') }}
                                    </div>
                                    <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $trx->margin_persen }}% Margin
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
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
