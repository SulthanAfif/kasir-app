@extends('layouts.app')
@section('title', 'Buat Pembelian')
@section('content')
<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
@csrf
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal *</label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div></div>

        <div class="card"><div class="card-header bg-white d-flex justify-content-between">
            <h6 class="mb-0">Item Produk</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="bi bi-plus"></i> Tambah</button>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0" id="itemsTable">
                <thead><tr><th class="ps-3">Produk</th><th>Harga Modal</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                <tbody id="itemsBody"></tbody>
            </table>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <div class="mb-2"><label class="form-label small">Diskon</label>
                <input type="number" name="discount" id="discount" class="form-control" value="0" min="0"></div>
            <div class="mb-2"><label class="form-label small">Pajak</label>
                <input type="number" name="tax" id="tax" class="form-control" value="0" min="0"></div>
            <div class="mb-2"><label class="form-label small">Dibayar</label>
                <input type="number" name="paid" class="form-control" value="0" min="0"></div>
            <div class="mb-3"><label class="form-label small">Catatan</label>
                <textarea name="notes" class="form-control" rows="2"></textarea></div>
            <div class="d-flex justify-content-between fw-bold mb-3">
                <span>Total</span><span id="grandTotal">Rp 0</span>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="receive_now" value="1" id="receiveNow" checked>
                <label class="form-check-label" for="receiveNow">Terima barang sekarang (update stok)</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Pembelian</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
        </div></div>
    </div>
</div>
</form>
@endsection
@push('scripts')
<script>
const products = @json($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'code'=>$p->code,'cost'=>$p->cost]));
let rowIdx = 0;

function addRow() {
    const opts = products.map(p => `<option value="${p.id}" data-cost="${p.cost}">${p.code} - ${p.name}</option>`).join('');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="ps-3"><select name="items[${rowIdx}][product_id]" class="form-select form-select-sm prod-select" required onchange="onProdChange(this)">
            <option value="">Pilih</option>${opts}</select></td>
        <td><input type="number" name="items[${rowIdx}][cost]" class="form-control form-control-sm cost-input" value="0" min="0" step="100" oninput="calc()" required></td>
        <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control form-control-sm qty-input" value="1" min="1" oninput="calc()" required></td>
        <td class="line-sub">Rp 0</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();calc()"><i class="bi bi-x"></i></button></td>`;
    document.getElementById('itemsBody').appendChild(tr);
    rowIdx++;
}

function onProdChange(sel) {
    const cost = sel.selectedOptions[0]?.dataset.cost || 0;
    sel.closest('tr').querySelector('.cost-input').value = cost;
    calc();
}

function calc() {
    let sub = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const c = parseFloat(tr.querySelector('.cost-input')?.value) || 0;
        const q = parseInt(tr.querySelector('.qty-input')?.value) || 0;
        const line = c * q;
        sub += line;
        tr.querySelector('.line-sub').textContent = 'Rp ' + line.toLocaleString('id-ID');
    });
    const disc = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    document.getElementById('grandTotal').textContent = 'Rp ' + Math.max(0, sub - disc + tax).toLocaleString('id-ID');
}

document.getElementById('discount').addEventListener('input', calc);
document.getElementById('tax').addEventListener('input', calc);
addRow();
</script>
@endpush
