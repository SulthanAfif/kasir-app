@extends('layouts.app')
@section('title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    <h6 class="text-muted mb-3">Informasi Toko</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Toko *</label>
                            <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings['store_name']) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings['store_phone']) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="store_address" class="form-control" rows="2">{{ old('store_address', $settings['store_address']) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="store_email" class="form-control" value="{{ old('store_email', $settings['store_email']) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mata Uang</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings['currency']) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pajak / PPN (%) *</label>
                            <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings['tax_rate']) }}" min="0" max="100" step="0.01" required>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">Struk / Receipt</h6>
                    <div class="mb-4">
                        <label class="form-label">Footer Struk</label>
                        <textarea name="receipt_footer" class="form-control" rows="2">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
