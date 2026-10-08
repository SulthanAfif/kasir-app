@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="Invoice / Pelanggan..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td class="ps-3"><code>{{ $t->invoice_number }}</code></td>
                        <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->user->name }}</td>
                        <td>{{ $t->customer_name ?? '-' }}</td>
                        <td class="fw-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-light text-dark">{{ strtoupper($t->payment_method) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('transactions.receipt', $t) }}" target="_blank"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
