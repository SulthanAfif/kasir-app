@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-muted small">Penjualan Hari Ini</div>
                    <div class="fw-bold fs-5">Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small">Transaksi Hari Ini</div>
                    <div class="fw-bold fs-5">{{ $stats['today_transactions'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Produk</div>
                    <div class="fw-bold fs-5">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted small">Stok Menipis</div>
                    <div class="fw-bold fs-5">{{ $stats['low_stock'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-bar-chart me-1"></i> Penjualan 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="120"></canvas>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
                <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Transaksi Terbaru</h6>
                <a href="{{ route('transactions.index') }}" class="small">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Invoice</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $t)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('transactions.show', $t) }}">{{ $t->invoice_number }}</a>
                                </td>
                                <td>{{ $t->user->name }}</td>
                                <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                <td class="text-muted small">{{ $t->created_at->format('d/m H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Stok Menipis</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($lowStockProducts as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $p->name }}</div>
                            <small class="text-muted">{{ $p->category->name ?? '-' }}</small>
                        </div>
                        <span class="badge bg-danger">{{ $p->stock }} {{ $p->unit }}</span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">Semua stok aman 👍</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body text-center">
                <a href="{{ route('pos.index') }}" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-cart3 me-2"></i> Buka Kasir
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Penjualan (Rp)',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.7)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + v.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>
@endpush
