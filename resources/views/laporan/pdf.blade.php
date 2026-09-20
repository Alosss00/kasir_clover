<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan & Keuntungan - Kasir Clover</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 9px;
            color: #666;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .summary-title {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            margin-top: 1px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.data-table th {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-cash {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-debit {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KASIR CLOVER — LAPORAN PENJUALAN & KEUNTUNGAN</h1>
        <p>Periode: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <!-- Ringkasan Keuangan (Omzet, HPP, Keuntungan, Margin) -->
    <table class="summary-box">
        <tr>
            <td style="width: 25%;">
                <div class="summary-title">Total Omzet Penjualan</div>
                <div class="summary-value" style="color: #059669;">Rp {{ number_format($metrics['omzet'], 0, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6b7280;">{{ $metrics['total_transaksi'] }} Order ({{ $metrics['total_cup'] }} Cup)</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-title">Total Modal / HPP</div>
                <div class="summary-value" style="color: #d97706;">Rp {{ number_format($metrics['hpp'], 0, ',', '.') }}</div>
                <div style="font-size: 8px; color: #6b7280;">Biaya Pokok Bahan Baku</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-title">Keuntungan Bersih (Profit)</div>
                <div class="summary-value" style="color: #15803d;">Rp {{ number_format($metrics['profit'], 0, ',', '.') }}</div>
                <div style="font-size: 8px; color: #15803d; font-weight: bold;">Margin: {{ $metrics['margin'] }}%</div>
            </td>
            <td style="width: 25%;">
                <div class="summary-title">Metode Pembayaran</div>
                <div style="font-size: 9px; margin-top: 1px;">
                    <strong>Tunai:</strong> Rp {{ number_format($metrics['cash_omzet'], 0, ',', '.') }} ({{ $metrics['cash_count'] }})<br>
                    <strong>Debit/QRIS:</strong> Rp {{ number_format($metrics['debit_omzet'], 0, ',', '.') }} ({{ $metrics['debit_count'] }})
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 13%;">Kode Trx</th>
                <th style="width: 12%;">Waktu</th>
                <th style="width: 14%;">Customer / Kasir</th>
                <th>Rincian Pesanan Menu</th>
                <th style="width: 12%;" class="text-right">Omzet (Rp)</th>
                <th style="width: 11%;" class="text-right">Modal/HPP</th>
                <th style="width: 13%;" class="text-right">Profit (Rp)</th>
                <th style="width: 7%;" class="text-center">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $trx)
                <tr>
                    <td><strong>{{ $trx->kode_transaksi }}</strong></td>
                    <td>{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                    <td>
                        <strong>{{ $trx->nama_customer ?? 'Pelanggan Umum' }}</strong><br>
                        <span style="font-size: 8px; color: #6b7280;">Kasir: {{ $trx->kasir->name ?? 'Kasir' }}</span>
                    </td>
                    <td>
                        @foreach($trx->details as $d)
                            <div>• {{ $d->nama_menu_snapshot }} ({{ $d->qty }}x @ Rp {{ number_format($d->harga_satuan_snapshot, 0, ',', '.') }})</div>
                        @endforeach
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #111827;">
                        Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="color: #6b7280;">
                        Rp {{ number_format($trx->total_hpp, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #15803d;">
                        Rp {{ number_format($trx->total_profit, 0, ',', '.') }}<br>
                        <span style="font-size: 8px; color: #15803d;">({{ $trx->margin_persen }}%)</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $trx->metode_pembayaran === 'cash' ? 'badge-cash' : 'badge-debit' }}">
                            {{ strtoupper($trx->metode_pembayaran) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode terpilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Kasir Clover POS & Rekap Keuntungan &bull; Dokumen Rahasia Internal Kafe</p>
    </div>
</body>
</html>

