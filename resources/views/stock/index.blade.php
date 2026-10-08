@extends('layouts.app')
@section('title', 'Mutasi Stok')

@section('content')
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <select name="product_id" class="form-select" style="width:220px">
            <option value="">Semua Produk</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->code }} - {{ $p->name }}
                </option>
            @endforeach
        </select>
        <select name="type" class="form-select" style="width:160px">
            <option value="">Semua Tipe</option>
            <option value="in" {{ request('type')=='in'?'selected':'' }}>Stok Masuk</option>
            <option value="out" {{ request('type')=='out'?'selected':'' }}>Stok Keluar</option>
            <option value="adjustment" {{ request('type')=='adjustment'?'selected':'' }}>Penyesuaian</option>
            <option value="sale" {{ request('type')=='sale'?'selected':'' }}>Penjualan</option>
            <option value="refund" {{ request('type')=='refund'?'selected':'' }}>Refund</option>
        </select>
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('stock.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Sesuaikan Stok
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Waktu</th>
                        <th>Produk</th>
                        <th>Tipe</th>
                        <th>Qty</th>
                        <th>Sebelum → Sesudah</th>
                        <th>Oleh</th>
                        <th>Ref / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                    <tr>
                        <td class="ps-3 small">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $m->product->name ?? '-' }}</td>
                        <td>
                            @php
                                $badge = match($m->type) {
                                    'in' => 'success', 'out' => 'warning', 'adjustment' => 'info',
                                    'sale' => 'primary', 'refund' => 'secondary', default => 'dark'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $m->typeLabel() }}</span>
                        </td>
                        <td class="{{ $m->quantity > 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                            {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                        </td>
                        <td>{{ $m->stock_before }} → {{ $m->stock_after }}</td>
                        <td>{{ $m->user->name ?? '-' }}</td>
                        <td class="small text-muted">{{ $m->reference ?? $m->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada mutasi stok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movements->hasPages())
    <div class="card-footer bg-white">{{ $movements->links() }}</div>
    @endif
</div>
@endsection
