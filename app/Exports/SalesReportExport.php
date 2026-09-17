<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $metode;

    public function __construct($startDate, $endDate, $metode = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->metode = $metode;
    }

    public function query()
    {
        $query = Transaksi::query()
            ->with(['kasir', 'details'])
            ->whereDate('tanggal_transaksi', '>=', $this->startDate)
            ->whereDate('tanggal_transaksi', '<=', $this->endDate);

        if ($this->metode) {
            $query->where('metode_pembayaran', $this->metode);
        }

        return $query->orderBy('tanggal_transaksi', 'desc');
    }

    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Tanggal & Waktu',
            'Nama Customer',
            'Nama Kasir',
            'Rincian Menu (Qty x Harga)',
            'Total Belanja (Rp)',
            'Metode Pembayaran',
            'Nominal Bayar (Rp)',
            'Kembalian (Rp)',
            'Catatan',
        ];
    }

    public function map($transaksi): array
    {
        $detailString = $transaksi->details->map(function ($d) {
            return "{$d->nama_menu_snapshot} ({$d->qty}x @ Rp " . number_format($d->harga_satuan_snapshot, 0, ',', '.') . ")";
        })->implode(', ');

        return [
            $transaksi->kode_transaksi,
            $transaksi->tanggal_transaksi->format('d/m/Y H:i:s'),
            $transaksi->nama_customer ?? 'Pelanggan Umum',
            $transaksi->kasir->name ?? 'Kasir',
            $detailString,
            (float) $transaksi->total_harga,
            strtoupper($transaksi->metode_pembayaran),
            $transaksi->jumlah_bayar ? (float)$transaksi->jumlah_bayar : 'Non-Tunai',
            $transaksi->kembalian !== null ? (float)$transaksi->kembalian : 0,
            $transaksi->catatan ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']]],
        ];
    }
}
