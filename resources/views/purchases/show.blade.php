@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')
<div class="row justify-content-center"><div class="col-md-9">
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ $purchase->invoice_number }}
                <span class="badge bg-{{ $purchase->status=='received'?'success':($purchase->status=='draft'?'info':'danger') }}">{{ strtoupper($purchase->status) }}</span>
            </h5>
            <small class="text-muted">{{ $purchase->purchase_date->format('d F Y') }} · {{ $purchase->user->name }}</small>
        </div>
        <div class="d-flex gap-1">
            @if($purchase->status === 'draft')
            <form method="POST" action="{{ route('purchases.receive', $purchase) }}">@csrf
                <button class="btn btn-success btn-sm" onclick="return confirm('Terima barang & update stok?')"><i class="bi bi-box-seam"></i> Terima</button>
            </form>
            @endif
            @if($purchase->status !== 'cancelled')
            <form method="POST" action="{{ route('purchases.cancel', $purchase) }}">@csrf
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Batalkan PO?')"><i class="bi bi-x-circle"></i> Batal</button>
            </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6"><small class="text-muted">Supplier</small><div class="fw-semibold">{{ $purchase->supplier->name }}</div></div>
            <div class="col-md-6"><small class="text-muted">Pembayaran</small><div class="fw-semibold">{{ strtoupper($purchase->payment_status) }} — Dibayar Rp {{ number_format($purchase->paid, 0, ',', '.') }}</div></div>
        </div>
        <table class="table table-sm">
            <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Modal</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($purchase->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">Rp {{ number_format($item->cost, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td></tr>
                @if($purchase->discount > 0)<tr><td colspan="3" class="text-end">Diskon</td><td class="text-end text-danger">- Rp {{ number_format($purchase->discount, 0, ',', '.') }}</td></tr>@endif
                @if($purchase->tax > 0)<tr><td colspan="3" class="text-end">Pajak</td><td class="text-end">Rp {{ number_format($purchase->tax, 0, ',', '.') }}</td></tr>@endif
                <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white">
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>
</div></div>
@endsection
