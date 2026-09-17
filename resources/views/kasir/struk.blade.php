<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #{{ $transaksi->kode_transaksi }} - Kasir Clover</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0 auto;
            padding: 8px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #555;
            margin: 6px 0;
        }
        .header {
            margin-bottom: 6px;
        }
        .header h1 {
            font-size: 15px;
            font-weight: bold;
        }
        .header p {
            font-size: 10px;
        }
        .meta-row, .item-row, .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .item-row {
            flex-direction: column;
            margin-bottom: 4px;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
            padding-left: 6px;
        }
        .footer {
            margin-top: 8px;
            font-size: 10px;
            text-align: center;
        }
        .no-print {
            margin-top: 15px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            background: #059669;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-family: sans-serif;
            font-size: 11px;
            cursor: pointer;
            border: none;
        }
        @media print {
            .no-print { display: none !important; }
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header text-center">
        <h1>KASIR CLOVER</h1>
        <p>Coffee & Roastery</p>
        <p>Jl. Clover Raya No. 88</p>
    </div>

    <div class="divider"></div>

    <div class="meta-row">
        <span>No:</span>
        <span class="font-bold">{{ $transaksi->kode_transaksi }}</span>
    </div>
    <div class="meta-row">
        <span>Tgl:</span>
        <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</span>
    </div>
    <div class="meta-row">
        <span>Kasir:</span>
        <span>{{ $transaksi->kasir->name ?? 'Kasir' }}</span>
    </div>
    @if($transaksi->catatan)
    <div class="meta-row">
        <span>Note:</span>
        <span>{{ $transaksi->catatan }}</span>
    </div>
    @endif

    <div class="divider"></div>

    @foreach($transaksi->details as $d)
    <div class="item-row">
        <span class="font-bold">{{ $d->nama_menu_snapshot }}</span>
        <div class="item-details">
            <span>{{ $d->qty }} x {{ number_format($d->harga_satuan_snapshot, 0, ',', '.') }}</span>
            <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="total-row font-bold">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
    </div>
    <div class="total-row">
        <span>Metode:</span>
        <span>{{ strtoupper($transaksi->metode_pembayaran) }}</span>
    </div>

    @if($transaksi->metode_pembayaran === 'cash')
    <div class="total-row">
        <span>Bayar:</span>
        <span>Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
    </div>
    <div class="total-row font-bold">
        <span>Kembali:</span>
        <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
    </div>
    @endif

    <div class="divider"></div>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Nikmati harimu bersama Kasir Clover</p>
    </div>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Cetak Ulang</button>
        <button class="btn" style="background: #334155;" onclick="window.close()">Tutup</button>
    </div>

</body>
</html>
