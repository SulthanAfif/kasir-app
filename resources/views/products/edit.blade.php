@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Produk *</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code', $product->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nama Produk *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori *</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Harga Jual *</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required min="0" step="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Harga Modal</label>
                            <input type="number" name="cost" class="form-control" value="{{ old('cost', $product->cost) }}" min="0" step="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stok *</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Stok Minimal *</label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Satuan *</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if($product->image)
                                <div class="form-text">Gambar saat ini ada</div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Update Produk</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
