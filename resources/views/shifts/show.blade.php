@extends('layouts.app')
@section('title', 'Detail Shift')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0">Shift #{{ $shift->id }} — {{ $shift->user->name }}</h5>
                <span class="badge {{ $shift->status === 'open' ? 'bg-success' : 'bg-secondary' }}">
                    {{ strtoupper($shift->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Dibuka</small>
                        <div class="fw-semibold">{{ $shift->opened_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Ditutup</small>
                        <div class="fw-semibold">{{ $shift->closed_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Modal Awal</small>
                        <div class="fw-semibold">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Uang Tutup</small>
                        <div class="fw-semibold">Rp {{ number_format($shift->closing_cash ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Expected Cash</small>
                        <div class="fw-semibold">Rp {{ number_format($shift->expected_cash ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Selisih</small>
                        <div class="fw-semibold {{ ($shift->difference ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($shift->difference ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Total Transaksi</small>
                        <div class="fw-semibold">{{ $shift->total_transactions }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">Total Penjualan</small>
                        <div class="fw-semibold">Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</div>
                    </div>
                </div>

                @if($shift->notes)
                <div class="alert alert-light">{{ $shift->notes }}</div>
                @endif

                <h6>Transaksi dalam Shift</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shift->transactions as $t)
                        <tr>
                            <td><a href="{{ route('transactions.show', $t) }}">{{ $t->invoice_number }}</a></td>
                            <td>{{ $t->created_at->format('H:i') }}</td>
                            <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($t->payment_method) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
