<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Kasir Clover</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #059669;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .summary-title {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KASIR CLOVER — LAPORAN PENJUALAN</h1>
        <p>Periode Transaksi: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Ringkasan Keuangan -->
    <table class="summary-box">
        <tr>
            <td>
                <div class="summary-title">Total Omzet Penjualan</div>
                <div class="summary-value" style="color: #059669;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-title">Total Transaksi</div>
                <div class="summary-value">{{ $totalTransaksi }} Order</div>
            </td>
            <td>
                <div class="summary-title">Pembayaran Tunai (Cash)</div>
                <div class="summary-value">Rp {{ number_format($cashOmzet, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-title">Debit / QRIS Digital</div>
                <div class="summary-value">Rp {{ number_format($debitOmzet, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 13%;">Kode Trx</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 14%;">Customer</th>
                <th style="width: 10%;">Kasir</th>
                <th>Rincian Pesanan Menu</th>
                <th style="width: 8%;" class="text-center">Metode</th>
                <th style="width: 13%;" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $trx)
                <tr>
                    <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                    <td>{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $trx->nama_customer ?? 'Pelanggan Umum' }}</strong></td>
                    <td>{{ $trx->kasir->name ?? 'Kasir' }}</td>
                    <td>
                        @foreach($trx->details as $d)
                            <div>• {{ $d->nama_menu_snapshot }} ({{ $d->qty }}x @ Rp {{ number_format($d->harga_satuan_snapshot, 0, ',', '.') }})</div>
                        @endforeach
                    </td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $trx->metode_pembayaran }}</td>
                    <td class="text-right" style="font-weight: bold; color: #059669;">
                        Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode terpilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Kasir Clover POS System &bull; Dokumen Rahasia Internal Kafe</p>
    </div>
</body>
</html>
