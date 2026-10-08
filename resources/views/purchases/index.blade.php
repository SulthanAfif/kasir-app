@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" style="width:180px" placeholder="No. PO..." value="{{ request('search') }}">
        <select name="status" class="form-select" style="width:140px">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            <option value="received" {{ request('status')=='received'?'selected':'' }}>Diterima</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Batal</option>
        </select>
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pembelian</a>
</div>
<div class="card"><div class="card-body p-0">
<table class="table table-hover mb-0">
<thead><tr><th class="ps-3">No. PO</th><th>Tanggal</th><th>Supplier</th><th>Total</th><th>Bayar</th><th>Status</th><th></th></tr></thead>
<tbody>
@forelse($purchases as $p)
<tr>
    <td class="ps-3"><code>{{ $p->invoice_number }}</code></td>
    <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
    <td>{{ $p->supplier->name ?? '-' }}</td>
    <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>
    <td><span class="badge bg-{{ $p->payment_status=='paid'?'success':($p->payment_status=='partial'?'warning':'secondary') }}">{{ strtoupper($p->payment_status) }}</span></td>
    <td><span class="badge bg-{{ $p->status=='received'?'success':($p->status=='draft'?'info':'danger') }}">{{ strtoupper($p->status) }}</span></td>
    <td><a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">Belum ada pembelian</td></tr>
@endforelse
</tbody></table>
</div>
@if($purchases->hasPages())<div class="card-footer bg-white">{{ $purchases->links() }}</div>@endif
</div>
@endsection
