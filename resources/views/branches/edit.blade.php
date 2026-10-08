@extends('layouts.app')
@section('title', 'Edit Cabang')
@section('content')
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('branches.update', $branch) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Kode</label>
        <input type="text" class="form-control" value="{{ $branch->code }}" disabled></div>
    <div class="mb-3"><label class="form-label">Nama Cabang *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Alamat</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $branch->address) }}</textarea></div>
    <div class="mb-3"><label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}"></div>
    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }}>
        <label class="form-check-label">Aktif</label></div>
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="is_main" value="1" {{ $branch->is_main ? 'checked' : '' }}>
        <label class="form-check-label">Jadikan Cabang Utama</label></div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Batal</a>
</form>
</div></div></div></div>
@endsection
