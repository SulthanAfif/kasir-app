@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('content')
<div class="row justify-content-center"><div class="col-md-6"><div class="card"><div class="card-body">
<form method="POST" action="{{ route('suppliers.update', $supplier) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Kode</label><input type="text" class="form-control" value="{{ $supplier->code }}" disabled></div>
    <div class="mb-3"><label class="form-label">Nama *</label><input type="text" name="name" class="form-control" required value="{{ old('name', $supplier->name) }}"></div>
    <div class="mb-3"><label class="form-label">Kontak Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}"></div>
    <div class="mb-3"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}"></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}"></div>
    <div class="mb-3"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea></div>
    <div class="mb-3"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes) }}</textarea></div>
    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $supplier->is_active ? 'checked' : '' }}><label class="form-check-label">Aktif</label></div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Batal</a>
</form>
</div></div></div></div>
@endsection
