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
                $startDate = $request->input('tanggal') ? Carbon::parse($request->input('tanggal'))->startOfDay() : $today->copy()->startOfDay();
                $endDate = $startDate->copy()->endOfDay();
                break;

            case 'mingguan':
                $startDate = $today->copy()->subDays(6)->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;

            case 'tahunan':
                $year = $request->input('tahun', date('Y'));
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                break;

            case 'custom':
                $startDate = $request->input('dari') ? Carbon::parse($request->input('dari'))->startOfDay() : $today->copy()->subDays(30)->startOfDay();
                $endDate = $request->input('sampai') ? Carbon::parse($request->input('sampai'))->endOfDay() : $today->copy()->endOfDay();
                break;

            case 'bulanan':
            default:
                $bulan = $request->input('bulan', date('m'));
                $tahun = $request->input('tahun', date('Y'));
                $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
                break;
        }

        // Agregasi SQL Database (Hindari penarikan seluruh baris ke memori PHP)
        $baseQuery = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate]);

        $totalOmzet = (float) (clone $baseQuery)->sum('total_harga');
        $totalTransaksi = (int) (clone $baseQuery)->count();

        // Breakdown Metode Pembayaran
        $cashOmzet = (float) (clone $baseQuery)->where('metode_pembayaran', 'cash')->sum('total_harga');
        $cashCount = (int) (clone $baseQuery)->where('metode_pembayaran', 'cash')->count();

        $debitOmzet = (float) (clone $baseQuery)->where('metode_pembayaran', 'debit_qris')->sum('total_harga');
        $debitCount = (int) (clone $baseQuery)->where('metode_pembayaran', 'debit_qris')->count();

        // Tren Penjualan Harian untuk Grafik / Bar Chart
        $trendHarian = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal_transaksi) as date, SUM(total_harga) as total, COUNT(id) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Top 5 Menu Terlaris via Agregasi SQL Detail
        $topMenus = TransaksiDetail::whereHas('transaksi', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            })
            ->selectRaw('nama_menu_snapshot, SUM(qty) as total_qty, SUM(subtotal) as total_omzet')
            ->groupBy('nama_menu_snapshot')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Transaksi List dengan Pagination & Eager Loading (Cegah N+1)
        $transaksiList = Transaksi::with(['kasir', 'details'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('laporan.penjualan', compact(
            'filterType',
            'startDate',
            'endDate',
            'totalOmzet',
            'totalTransaksi',
            'cashOmzet',
            'cashCount',
            'debitOmzet',
            'debitCount',
            'trendHarian',
            'topMenus',
            'transaksiList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $metode = $request->input('metode');

        $fileName = "laporan-penjualan-{$startDate}_sd_{$endDate}.xlsx";

        return Excel::download(new SalesReportExport($startDate, $endDate, $metode), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay();

        $transaksiList = Transaksi::with(['kasir', 'details'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        $totalOmzet = $transaksiList->sum('total_harga');
        $totalTransaksi = $transaksiList->count();
        $cashOmzet = $transaksiList->where('metode_pembayaran', 'cash')->sum('total_harga');
        $debitOmzet = $transaksiList->where('metode_pembayaran', 'debit_qris')->sum('total_harga');

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'transaksiList',
            'startDate',
            'endDate',
            'totalOmzet',
            'totalTransaksi',
            'cashOmzet',
            'debitOmzet'
        ))->setPaper('a4', 'portrait');

        $fileName = "laporan-penjualan-{$startDate->format('Y-m-d')}_sd_{$endDate->format('Y-m-d')}.pdf";
        return $pdf->download($fileName);
    }
}
