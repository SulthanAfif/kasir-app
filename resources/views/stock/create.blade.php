@extends('layouts.app')
@section('title', 'Sesuaikan Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('stock.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Produk *</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id')==$p->id?'selected':'' }}>
                                    {{ $p->code }} — {{ $p->name }} (Stok: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe *</label>
                        <select name="type" id="stockType" class="form-select" required>
                            <option value="in">Stok Masuk (Pembelian / Restock)</option>
                            <option value="out">Stok Keluar (Rusak / Hilang)</option>
                            <option value="adjustment">Penyesuaian (Set stok absolut)</option>
                        </select>
                        <div class="form-text" id="typeHelp">Masukkan jumlah yang ditambahkan ke stok.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" id="qtyLabel">Jumlah *</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('stockType').addEventListener('change', function() {
    const help = document.getElementById('typeHelp');
    const label = document.getElementById('qtyLabel');
    if (this.value === 'in') {
        help.textContent = 'Masukkan jumlah yang ditambahkan ke stok.';
        label.textContent = 'Jumlah Masuk *';
    } else if (this.value === 'out') {
        help.textContent = 'Masukkan jumlah yang dikurangi dari stok.';
        label.textContent = 'Jumlah Keluar *';
    } else {
        help.textContent = 'Masukkan jumlah stok absolut yang diinginkan (hasil stock opname).';
        label.textContent = 'Stok Baru *';
    }
});
</script>
@endpush
