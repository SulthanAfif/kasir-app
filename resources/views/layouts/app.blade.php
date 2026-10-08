<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Kasir') - KasirApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --primary: #0d6efd; --sidebar-bg: #1e293b; }
        body { background: #f1f5f9; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-width); background: var(--sidebar-bg); min-height: 100vh;
            position: fixed; top: 0; left: 0; z-index: 1000; overflow-y: auto;
        }
        .sidebar .brand { padding: 1.25rem; color: #fff; font-weight: 700; font-size: 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .nav-link {
            color: #94a3b8; padding: 0.6rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;
            transition: all 0.2s; font-size: 0.92rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { border-left: 3px solid var(--primary); }
        .sidebar .nav-section { padding: 0.7rem 1.25rem 0.2rem; font-size: 0.68rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; }
        .main-content { margin-left: var(--sidebar-width); padding: 1.5rem; min-height: 100vh; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .table th { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
        .badge-role-admin { background: #dbeafe; color: #1e40af; }
        .badge-role-kasir { background: #dcfce7; color: #166534; }
        .low-stock { color: #dc2626; font-weight: 600; }
        .pos-product-card { cursor: pointer; transition: all 0.15s; border: 2px solid transparent; }
        .pos-product-card:hover { border-color: var(--primary); transform: scale(1.02); }
        .cart-item { border-bottom: 1px solid #e2e8f0; padding: 0.75rem 0; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
@auth
<div class="sidebar" id="sidebar">
    <div class="brand"><i class="bi bi-shop"></i> KasirApp</div>
    <nav class="mt-2 pb-4">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i> Notifikasi
            <span class="badge bg-danger ms-auto" id="notifBadge" style="display:none">0</span>
        </a>

        <div class="nav-section">Operasional</div>
        <a href="{{ route('shifts.index') }}" class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Shift Kasir</a>
        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}"><i class="bi bi-cart3"></i> Kasir / POS</a>
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Transaksi</a>

        <div class="nav-section">Inventori</div>
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Produk</a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Kategori</a>
        <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Mutasi Stok</a>
        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}"><i class="bi bi-truck"></i> Pembelian</a>
        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Supplier</a>

        <div class="nav-section">Lainnya</div>
        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Pelanggan</a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> Laporan</a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section">Admin</div>
        <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i> Cabang</a>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Pengguna</a>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan</a>
        @endif

        <hr class="mx-3" style="border-color: rgba(255,255,255,0.1)">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </nav>
</div>
@endauth

<div class="@auth main-content @endauth">
    @auth
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <button class="btn btn-outline-secondary d-md-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <h4 class="mb-0 d-inline">@yield('title')</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">{{ auth()->user()->name }}</span>
            <span class="badge {{ auth()->user()->isAdmin() ? 'badge-role-admin' : 'badge-role-kasir' }}">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
    </div>
    @endauth

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@auth
<script>
fetch('{{ route("notifications.unreadCount") }}')
    .then(r => r.json())
    .then(d => {
        if (d.count > 0) {
            const b = document.getElementById('notifBadge');
            b.textContent = d.count;
            b.style.display = 'inline';
        }
    }).catch(() => {});
</script>
@endauth
@stack('scripts')
</body>
</html>
