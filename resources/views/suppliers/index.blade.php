@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" style="width:220px" placeholder="Cari..." value="{{ request('search') }}">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Supplier</a>
</div>
<div class="card"><div class="card-body p-0">
<table class="table table-hover mb-0">
<thead><tr><th class="ps-3">Kode</th><th>Nama</th><th>Kontak</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
@forelse($suppliers as $s)
<tr>
    <td class="ps-3"><code>{{ $s->code }}</code></td>
    <td class="fw-semibold">{{ $s->name }}</td>
    <td>{{ $s->contact_person ?? '-' }}</td>
    <td>{{ $s->phone ?? '-' }}</td>
    <td>@if($s->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Nonaktif</span>@endif</td>
    <td>
        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
        <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted py-4">Belum ada supplier</td></tr>
@endforelse
</tbody></table>
</div>
@if($suppliers->hasPages())<div class="card-footer bg-white">{{ $suppliers->links() }}</div>@endif
</div>
@endsection
