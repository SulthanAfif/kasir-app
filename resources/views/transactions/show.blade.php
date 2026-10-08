@extends('layouts.app')
@section('title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $transaction->invoice_number }}
                        @if($transaction->status === 'cancelled')
                            <span class="badge bg-danger">Dibatalkan / Refund</span>
                        @endif
                    </h5>
                    <small class="text-muted">{{ $transaction->created_at->format('d F Y, H:i') }}</small>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                    @if($transaction->isRefundable())
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#refundModal">
                        <i class="bi bi-arrow-counterclockwise"></i> Refund
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Kasir</small>
                        <div class="fw-semibold">{{ $transaction->user->name }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Pelanggan</small>
                        <div class="fw-semibold">{{ $transaction->customer_name ?? ($transaction->customer->name ?? '-') }}</div>
                    </div>
                </div>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td class="text-end">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaction->discount > 0)
                        <tr>
                            <td colspan="3" class="text-end">Diskon</td>
                            <td class="text-end text-danger">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($transaction->tax > 0)
                        <tr>
                            <td colspan="3" class="text-end">Pajak ({{ $transaction->tax_rate }}%)</td>
                            <td class="text-end">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Bayar ({{ strtoupper($transaction->payment_method) }})</td>
                            <td class="text-end">Rp {{ number_format($transaction->paid, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Kembalian</td>
                            <td class="text-end">Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if($transaction->notes)
                <div class="alert alert-light small mb-0">{{ $transaction->notes }}</div>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

@if($transaction->isRefundable())
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('transactions.refund', $transaction) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Refund Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Stok produk akan dikembalikan. Lanjutkan?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan (opsional)</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
