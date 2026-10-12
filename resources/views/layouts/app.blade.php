@php($u = auth()->user())
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title') · SyfaBuild</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark">S</span><span>SyfaBuild<small>BUILDING SUPPLY</small></span>
        </a>
        <div class="side-label">MENU UTAMA</div>
        <nav>
            <a href="{{ route('dashboard') }}">▦ <span>Dashboard</span></a>
            @if (in_array($u->role, ['owner', 'gudang']))
                <a href="{{ route('products.index') }}">▤ <span>Produk</span></a>
                <a href="{{ route('categories.index') }}">▧ <span>Kategori</span></a>
                <a href="{{ route('suppliers.index') }}">♧ <span>Supplier</span></a>
                <a href="{{ route('stock.index') }}">↕ <span>Stok masuk/keluar</span></a>
            @endif
            @if ($u->role === 'kasir')
                <a href="{{ route('sales.create') }}">▣ <span>Penjualan</span></a>
            @endif
            @if (in_array($u->role, ['owner', 'kasir']))
                <a href="{{ route('sales.index') }}">◷ <span>Riwayat penjualan</span></a>
            @endif
            <a class="nav-logout" href="#" onclick="event.preventDefault(); if (confirm('Yakin ingin keluar?')) document.getElementById('logout-form').submit();">⎋ <span>Keluar</span></a>
        </nav>
        <div class="sidebar-bottom">
            <div class="avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
            <div class="user-info"><strong>{{ $u->name }}</strong><small>{{ ucfirst($u->role) }}</small></div>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div><span class="eyebrow">INVENTORY MANAGEMENT</span><h1>@yield('title')</h1></div>
            <div class="top-actions">
                <div class="top-user"><span class="status-dot"></span>{{ $u->name }} ({{ ucfirst($u->role) }})</div>
                <a class="btn logout-btn" href="#" onclick="event.preventDefault(); if (confirm('Yakin ingin keluar?')) document.getElementById('logout-form').submit();">⎋ Keluar</a>
            </div>
        </header>

        <section class="content">
            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert type="error">Terdapat kesalahan pada isian. Periksa pesan di bawah kolom yang bermasalah.</x-alert>
            @endif

            @yield('content')
        </section>

        <footer class="footer">SyfaBuild · Sistem Manajemen Persediaan Bahan Bangunan</footer>
    </main>
</div>

<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
@stack('scripts')
</body>
</html>
