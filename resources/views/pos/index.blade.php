@extends('layouts.app')
@section('title', 'Kasir / POS')

@section('content')
<div class="alert alert-info py-2 mb-3 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-clock me-1"></i> Shift aktif sejak {{ $shift->opened_at->format('H:i') }} — Modal: Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</span>
    <span class="small">Pajak: {{ $taxRate }}%</span>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchProduct" class="form-control form-control-lg"
                           placeholder="🔍 Scan barcode / cari nama produk..." autofocus>
                </div>
                <div class="row g-2" id="productGrid" style="max-height: 60vh; overflow-y: auto;">
                    @foreach($products as $p)
                    <div class="col-6 col-md-4 col-xl-3 product-item"
                         data-id="{{ $p->id }}"
                         data-name="{{ $p->name }}"
                         data-price="{{ $p->price }}"
                         data-stock="{{ $p->stock }}"
                         data-code="{{ $p->code }}"
                         data-unit="{{ $p->unit }}">
                        <div class="card pos-product-card h-100" onclick="addToCart(this.closest('.product-item'))">
                            <div class="card-body p-2 text-center">
                                <div class="fw-semibold small text-truncate">{{ $p->name }}</div>
                                <div class="text-primary fw-bold">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                <div class="text-muted" style="font-size:0.75rem">{{ $p->code }} · Stok: {{ $p->stock }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 1rem;">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <span><i class="bi bi-cart3 me-1"></i> Keranjang</span>
                <button class="btn btn-sm btn-outline-light py-0" onclick="resetCart()" title="Kosongkan">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body" style="max-height: 32vh; overflow-y: auto;" id="cartItems">
                <p class="text-muted text-center my-4" id="emptyCart">Keranjang kosong</p>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Subtotal</span>
                    <span id="subtotalText">Rp 0</span>
                </div>
                <div class="mb-1">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Diskon</span>
                        <input type="number" id="discountInput" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-1 small" id="taxRow" style="{{ $taxRate > 0 ? '' : 'display:none' }}">
                    <span>Pajak ({{ $taxRate }}%)</span>
                    <span id="taxText">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-2">
                    <span>Total</span>
                    <span id="totalText" class="text-primary">Rp 0</span>
                </div>

                <div class="mb-2">
                    <label class="form-label small mb-0">Pelanggan</label>
                    <input type="text" id="customerSearch" class="form-control form-control-sm" placeholder="Cari / ketik nama...">
                    <input type="hidden" id="customerId" value="">
                    <div id="customerResults" class="list-group position-absolute shadow-sm" style="z-index:10; display:none; max-width:100%;"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-0">Metode Bayar</label>
                    <select id="paymentMethod" class="form-select form-select-sm">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small mb-0">Bayar (Rp)</label>
                    <input type="number" id="paidInput" class="form-control" value="0" min="0">
                    <div class="d-flex gap-1 mt-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPaidExact()">Pas</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPaidRound(1000)">+1rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPaidRound(5000)">+5rb</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="setPaidRound(10000)">+10rb</button>
                    </div>
                    <div class="text-muted small mt-1">Kembalian: <strong id="changeText">Rp 0</strong></div>
                </div>
                <button class="btn btn-success w-100 btn-lg" id="btnCheckout" onclick="checkout()" disabled>
                    <i class="bi bi-check2-circle me-1"></i> Bayar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-2">Transaksi Berhasil!</h5>
                <p class="text-muted" id="successInvoice"></p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="#" id="receiptLink" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> Cetak Struk
                    </a>
                    <button class="btn btn-primary" data-bs-dismiss="modal" onclick="resetCart(); location.reload();">
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const TAX_RATE = {{ $taxRate }};
let cart = [];

function formatRp(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function addToCart(el) {
    const id = parseInt(el.dataset.id);
    const stock = parseInt(el.dataset.stock);
    const existing = cart.find(i => i.product_id === id);
    if (existing) {
        if (existing.quantity >= stock) { alert('Stok tidak mencukupi!'); return; }
        existing.quantity++;
    } else {
        cart.push({
            product_id: id, name: el.dataset.name, price: parseFloat(el.dataset.price),
            stock: stock, unit: el.dataset.unit, quantity: 1
        });
    }
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(i => i.product_id === id);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) cart = cart.filter(i => i.product_id !== id);
    else if (item.quantity > item.stock) { item.quantity = item.stock; alert('Stok maksimal: ' + item.stock); }
    renderCart();
}

function removeItem(id) {
    cart = cart.filter(i => i.product_id !== id);
    renderCart();
}

function getSubtotal() {
    return cart.reduce((s, i) => s + (i.price * i.quantity), 0);
}

function getTax() {
    const afterDiscount = Math.max(0, getSubtotal() - (parseFloat(document.getElementById('discountInput').value) || 0));
    return Math.round(afterDiscount * (TAX_RATE / 100));
}

function getTotal() {
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    return Math.max(0, getSubtotal() - discount) + getTax();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-muted text-center my-4" id="emptyCart">Keranjang kosong</p>';
        document.getElementById('btnCheckout').disabled = true;
    } else {
        let html = '';
        cart.forEach(item => {
            html += `<div class="cart-item">
                <div class="d-flex justify-content-between">
                    <div class="fw-semibold small">${item.name}</div>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${item.product_id})"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQty(${item.product_id}, -1)">−</button>
                        <button class="btn btn-outline-secondary disabled">${item.quantity}</button>
                        <button class="btn btn-outline-secondary" onclick="updateQty(${item.product_id}, 1)">+</button>
                    </div>
                    <span class="fw-semibold">${formatRp(item.price * item.quantity)}</span>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        document.getElementById('btnCheckout').disabled = false;
    }
    document.getElementById('subtotalText').textContent = formatRp(getSubtotal());
    document.getElementById('taxText').textContent = formatRp(getTax());
    document.getElementById('totalText').textContent = formatRp(getTotal());
    updateChange();
}

document.getElementById('discountInput').addEventListener('input', () => {
    document.getElementById('taxText').textContent = formatRp(getTax());
    document.getElementById('totalText').textContent = formatRp(getTotal());
    updateChange();
});
document.getElementById('paidInput').addEventListener('input', updateChange);

function updateChange() {
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    document.getElementById('changeText').textContent = formatRp(Math.max(0, paid - getTotal()));
}

function setPaidExact() {
    document.getElementById('paidInput').value = getTotal();
    updateChange();
}
function setPaidRound(extra) {
    const total = getTotal();
    document.getElementById('paidInput').value = Math.ceil(total / extra) * extra;
    updateChange();
}

// Barcode / search: Enter adds first match
document.getElementById('searchProduct').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const q = this.value.toLowerCase().trim();
        if (!q) return;
        const items = [...document.querySelectorAll('.product-item')].filter(el =>
            el.dataset.code.toLowerCase() === q || el.dataset.name.toLowerCase().includes(q)
        );
        if (items.length === 1 || (items.length > 0 && items[0].dataset.code.toLowerCase() === q)) {
            addToCart(items[0]);
            this.value = '';
        }
    }
});

document.getElementById('searchProduct').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(el => {
        const match = el.dataset.name.toLowerCase().includes(q) || el.dataset.code.toLowerCase().includes(q);
        el.style.display = match ? '' : 'none';
    });
});

// Customer search
let customerTimer;
document.getElementById('customerSearch').addEventListener('input', function() {
    clearTimeout(customerTimer);
    const q = this.value.trim();
    document.getElementById('customerId').value = '';
    if (q.length < 2) { document.getElementById('customerResults').style.display = 'none'; return; }
    customerTimer = setTimeout(() => {
        fetch('{{ route("customers.search") }}?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('customerResults');
                if (!data.length) { box.style.display = 'none'; return; }
                box.innerHTML = data.map(c =>
                    `<a href="#" class="list-group-item list-group-item-action py-1" onclick="selectCustomer(${c.id}, '${c.name.replace(/'/g,"\\'")}'); return false;">
                        ${c.name} <small class="text-muted">${c.phone || c.code}</small>
                    </a>`
                ).join('');
                box.style.display = 'block';
            });
    }, 300);
});

function selectCustomer(id, name) {
    document.getElementById('customerId').value = id;
    document.getElementById('customerSearch').value = name;
    document.getElementById('customerResults').style.display = 'none';
}

function checkout() {
    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    const total = getTotal();
    if (paid < total) { alert('Pembayaran kurang!'); return; }
    if (cart.length === 0) return;

    const data = {
        items: cart.map(i => ({ product_id: i.product_id, quantity: i.quantity })),
        customer_id: document.getElementById('customerId').value || null,
        customer_name: document.getElementById('customerSearch').value || null,
        discount: parseFloat(document.getElementById('discountInput').value) || 0,
        paid: paid,
        payment_method: document.getElementById('paymentMethod').value,
    };

    fetch('{{ route("pos.checkout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('successInvoice').textContent = res.invoice;
            document.getElementById('receiptLink').href = '/transactions/' + res.transaction_id + '/receipt';
            new bootstrap.Modal(document.getElementById('successModal')).show();
        } else {
            alert(res.message || 'Gagal memproses transaksi');
        }
    })
    .catch(() => alert('Terjadi kesalahan. Silakan coba lagi.'));
}

function resetCart() {
    cart = [];
    document.getElementById('discountInput').value = 0;
    document.getElementById('paidInput').value = 0;
    document.getElementById('customerSearch').value = '';
    document.getElementById('customerId').value = '';
    renderCart();
}
</script>
@endpush
