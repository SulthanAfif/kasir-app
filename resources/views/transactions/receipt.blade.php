<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 10px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; }
        td { padding: 2px 0; vertical-align: top; }
        .right { text-align: right; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="center bold">
        <div style="font-size:16px">{{ $store['name'] }}</div>
        @if($store['address'])<div>{{ $store['address'] }}</div>@endif
        @if($store['phone'])<div>Telp: {{ $store['phone'] }}</div>@endif
    </div>
    <div class="line"></div>
    <div>
        Invoice: {{ $transaction->invoice_number }}<br>
        Tanggal: {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
        Kasir: {{ $transaction->user->name }}<br>
        @if($transaction->customer_name)
        Pelanggan: {{ $transaction->customer_name }}<br>
        @endif
    </div>
    <div class="line"></div>
    <table>
        @foreach($transaction->items as $item)
        <tr>
            <td colspan="2">{{ $item->product_name }}</td>
        </tr>
        <tr>
            <td>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    <div class="line"></div>
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount > 0)
        <tr>
            <td>Diskon</td>
            <td class="right">-{{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($transaction->tax > 0)
        <tr>
            <td>Pajak ({{ $transaction->tax_rate }}%)</td>
            <td class="right">{{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="bold">
            <td>TOTAL</td>
            <td class="right">{{ number_format($transaction->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($transaction->payment_method) }})</td>
            <td class="right">{{ number_format($transaction->paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">{{ number_format($transaction->change, 0, ',', '.') }}</td>
        </tr>
    </table>
    <div class="line"></div>
    <div class="center">{{ $store['footer'] }}</div>
    <div class="no-print center" style="margin-top:20px">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
