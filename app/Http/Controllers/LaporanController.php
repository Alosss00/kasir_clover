<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        $filterType = $request->input('filter_type', 'bulanan'); // harian, mingguan, bulanan, tahunan, custom
        $today = Carbon::today();

        switch ($filterType) {
            case 'harian':
                $targetDate = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : $today;
                $startDate = $targetDate->copy()->startOfDay();
                $endDate = $targetDate->copy()->endOfDay();
                break;

            case 'mingguan':
                $startDate = $today->copy()->subDays(6)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;

            case 'tahunan':
                $year = (int) $request->input('tahun', date('Y'));
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                break;

            case 'custom':
                $startDate = $request->input('dari') ? Carbon::parse($request->input('dari'))->startOfDay() : $today->copy()->subDays(30)->startOfDay();
                $endDate = $request->input('sampai') ? Carbon::parse($request->input('sampai'))->endOfDay() : $today->copy()->endOfDay();
                break;

            case 'bulanan':
            default:
                $bulan = (int) $request->input('bulan', date('m'));
                $tahun = (int) $request->input('tahun', date('Y'));
                $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
                break;
        }

        // 1. Ringkasan Keuntungan Multi-Periode Cepat (Harian, Mingguan, Bulanan, Tahunan)
        $summaryHarian = $this->getPeriodMetrics($today->copy()->startOfDay(), $today->copy()->endOfDay());
        $summaryMingguan = $this->getPeriodMetrics($today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay());
        $summaryBulanan = $this->getPeriodMetrics(Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfMonth()->endOfDay());
        $summaryTahunan = $this->getPeriodMetrics(Carbon::now()->startOfYear()->startOfDay(), Carbon::now()->endOfYear()->endOfDay());

        // 2. Metrik Keuangan Periode Terpilih (Filter Aktif)
        $activeMetrics = $this->getPeriodMetrics($startDate, $endDate);

        // 3. Tren Penjualan & Keuntungan Harian untuk Grafik / Tabel Tren
        $trendHarian = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->leftJoin('menu', 'menu.id', '=', 'transaksi_detail.menu_id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('
                DATE(transaksi.tanggal_transaksi) as date,
                COUNT(DISTINCT transaksi.id) as count,
                COALESCE(SUM(transaksi_detail.subtotal), 0) as total_omzet,
                COALESCE(SUM(COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty), 0) as total_hpp,
                COALESCE(SUM(transaksi_detail.subtotal - (COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty)), 0) as total_profit
            ')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 4. Top 5 Menu Terlaris & Profit Maker via Agregasi SQL Detail
        $topMenus = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->leftJoin('menu', 'menu.id', '=', 'transaksi_detail.menu_id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('
                transaksi_detail.nama_menu_snapshot,
                SUM(transaksi_detail.qty) as total_qty,
                SUM(transaksi_detail.subtotal) as total_omzet,
                SUM(COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty) as total_hpp,
                SUM(transaksi_detail.subtotal - (COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty)) as total_profit
            ')
            ->groupBy('transaksi_detail.nama_menu_snapshot')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 5. Transaksi List dengan Pagination & Eager Loading (Cegah N+1)
        $transaksiList = Transaksi::with(['kasir', 'details.menu'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('laporan.penjualan', compact(
            'filterType',
            'startDate',
            'endDate',
            'summaryHarian',
            'summaryMingguan',
            'summaryBulanan',
            'summaryTahunan',
            'activeMetrics',
            'trendHarian',
            'topMenus',
            'transaksiList'
        ));
    }

    /**
     * Helper untuk menghitung metrik finansial (Omzet, HPP, Keuntungan, Margin, Metode Bayar)
     */
    private function getPeriodMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $detailStats = DB::table('transaksi_detail')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->leftJoin('menu', 'menu.id', '=', 'transaksi_detail.menu_id')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(transaksi_detail.subtotal), 0) as total_omzet,
                COALESCE(SUM(COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty), 0) as total_hpp,
                COALESCE(SUM(transaksi_detail.subtotal - (COALESCE(menu.cost_per_cup, transaksi_detail.cost_per_cup_snapshot, 0) * transaksi_detail.qty)), 0) as total_profit,
                COALESCE(SUM(transaksi_detail.qty), 0) as total_qty
            ')->first();

        $trxStats = DB::table('transaksi')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('
                COUNT(id) as total_transaksi,
                COALESCE(SUM(CASE WHEN metode_pembayaran = "cash" THEN total_harga ELSE 0 END), 0) as cash_omzet,
                COALESCE(SUM(CASE WHEN metode_pembayaran = "cash" THEN 1 ELSE 0 END), 0) as cash_count,
                COALESCE(SUM(CASE WHEN metode_pembayaran = "debit_qris" THEN total_harga ELSE 0 END), 0) as debit_omzet,
                COALESCE(SUM(CASE WHEN metode_pembayaran = "debit_qris" THEN 1 ELSE 0 END), 0) as debit_count
            ')->first();

        $omzet = (float) ($detailStats->total_omzet ?? 0);
        $hpp = (float) ($detailStats->total_hpp ?? 0);
        $profit = (float) ($detailStats->total_profit ?? 0);
        $margin = $omzet > 0 ? round(($profit / $omzet) * 100, 1) : 0;

        return [
            'omzet' => $omzet,
            'hpp' => $hpp,
            'profit' => $profit,
            'margin' => $margin,
            'total_transaksi' => (int) ($trxStats->total_transaksi ?? 0),
            'total_cup' => (int) ($detailStats->total_qty ?? 0),
            'cash_omzet' => (float) ($trxStats->cash_omzet ?? 0),
            'cash_count' => (int) ($trxStats->cash_count ?? 0),
            'debit_omzet' => (float) ($trxStats->debit_omzet ?? 0),
            'debit_count' => (int) ($trxStats->debit_count ?? 0),
        ];
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $metode = $request->input('metode');

        $fileName = "laporan-penjualan-profit-{$startDate}_sd_{$endDate}.xlsx";

        return Excel::download(new SalesReportExport($startDate, $endDate, $metode), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay();

        $metrics = $this->getPeriodMetrics($startDate, $endDate);

        $transaksiList = Transaksi::with(['kasir', 'details'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'transaksiList',
            'startDate',
            'endDate',
            'metrics'
        ))->setPaper('a4', 'portrait');

        $fileName = "laporan-penjualan-profit-{$startDate->format('Y-m-d')}_sd_{$endDate->format('Y-m-d')}.pdf";
        return $pdf->download($fileName);
    }
}
