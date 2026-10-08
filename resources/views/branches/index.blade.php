@extends('layouts.app')
@section('title', 'Cabang')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('branches.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Cabang</a>
</div>
<div class="card"><div class="card-body p-0">
<table class="table table-hover mb-0">
<thead><tr><th class="ps-3">Kode</th><th>Nama</th><th>Alamat</th><th>User</th><th>Produk</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
@forelse($branches as $b)
<tr>
    <td class="ps-3"><code>{{ $b->code }}</code> @if($b->is_main)<span class="badge bg-primary">Utama</span>@endif</td>
    <td class="fw-semibold">{{ $b->name }}</td>
    <td class="small">{{ $b->address ?? '-' }}</td>
    <td>{{ $b->users_count }}</td>
    <td>{{ $b->products_count }}</td>
    <td>@if($b->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Nonaktif</span>@endif</td>
    <td>
        <a href="{{ route('branches.edit', $b) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
        @unless($b->is_main)
        <form action="{{ route('branches.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
        </form>
        @endunless
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">Belum ada cabang</td></tr>
@endforelse
</tbody></table>
</div>
@if($branches->hasPages())<div class="card-footer bg-white">{{ $branches->links() }}</div>@endif
</div>
@endsection
