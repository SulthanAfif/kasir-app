@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-3">
        <label class="form-label small">Dari Tanggal</label>
        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
    </div>
    <div class="col-md-3">
        <label class="form-label small">Sampai Tanggal</label>
        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
    </div>
    <div class="col-md-2 d-flex align-items-end gap-1">
        <button class="btn btn-primary flex-fill"><i class="bi bi-funnel"></i> Filter</button>
    </div>
    <div class="col-md-4 d-flex align-items-end gap-1 justify-content-end">
        <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
        <a href="{{ route('reports.export.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small">Total Transaksi</div>
                <div class="fs-4 fw-bold">{{ number_format($summary->total_transactions ?? 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small">Total Penjualan</div>
                <div class="fs-4 fw-bold text-primary">Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small">Total Diskon</div>
                <div class="fs-4 fw-bold text-danger">Rp {{ number_format($summary->total_discount ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small">Rata-rata / Transaksi</div>
                <div class="fs-4 fw-bold">
                    Rp {{ number_format(($summary->total_transactions ?? 0) > 0 ? ($summary->total_sales / $summary->total_transactions) : 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Penjualan Harian</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Transaksi</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailySales as $d)
                        <tr>
                            <td class="ps-3">{{ \Carbon\Carbon::parse($d->date)->format('d M Y') }}</td>
                            <td>{{ $d->count }}</td>
                            <td class="text-end pe-3">Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Top 10 Produk Terlaris</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $p)
                        <tr>
                            <td class="ps-3">{{ $p->product_name }}</td>
                            <td class="text-center">{{ $p->total_qty }}</td>
                            <td class="text-end pe-3">Rp {{ number_format($p->total_sales, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Metode Pembayaran</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Metode</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-end pe-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $pm)
                        <tr>
                            <td class="ps-3">{{ strtoupper($pm->payment_method) }}</td>
                            <td class="text-center">{{ $pm->count }}</td>
                            <td class="text-end pe-3">Rp {{ number_format($pm->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
