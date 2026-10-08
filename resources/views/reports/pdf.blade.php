<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
        .right { text-align: right; }
        .summary { margin-bottom: 16px; }
        .summary td { border: none; padding: 3px 8px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:12px">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
        <button onclick="window.close()">Tutup</button>
    </div>
    <h1>{{ $storeName }}</h1>
    <div class="meta">Laporan Penjualan: {{ $dateFrom }} s/d {{ $dateTo }} · Dicetak {{ now()->format('d/m/Y H:i') }}</div>

    <table class="summary">
        <tr><td>Total Transaksi</td><td class="right"><strong>{{ number_format($summary->total_transactions ?? 0) }}</strong></td></tr>
        <tr><td>Total Penjualan</td><td class="right"><strong>Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}</strong></td></tr>
        <tr><td>Total Diskon</td><td class="right">Rp {{ number_format($summary->total_discount ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Total Pajak</td><td class="right">Rp {{ number_format($summary->total_tax ?? 0, 0, ',', '.') }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th class="right">Total</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->invoice_number }}</td>
                <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->user->name ?? '-' }}</td>
                <td>{{ $t->customer_name ?? '-' }}</td>
                <td class="right">{{ number_format($t->total, 0, ',', '.') }}</td>
                <td>{{ strtoupper($t->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
