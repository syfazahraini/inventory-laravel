@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="stats-grid three">
    <article class="stat-card"><div class="stat-icon">▤</div><span>Total Produk</span><strong>{{ $stats['products'] }}</strong><small>Jenis barang terdaftar</small></article>
    <article class="stat-card"><div class="stat-icon">◈</div><span>Nilai Persediaan</span><strong>@rupiah($stats['inventory_value'])</strong><small>Berdasarkan harga modal</small></article>
    <article class="stat-card"><div class="stat-icon">!</div><span>Stok Menipis</span><strong>{{ $stats['low_stock'] }}</strong><small>Perlu diperiksa</small></article>
</div>

<div class="stats-grid three">
    <article class="stat-card"><div class="stat-icon slate">↗</div><span>Penjualan Hari Ini</span><strong>@rupiah($sales['today']->total)</strong><small>{{ $sales['today']->cnt }} transaksi · hari ini</small></article>
    <article class="stat-card"><div class="stat-icon slate">↗</div><span>Penjualan Minggu Ini</span><strong>@rupiah($sales['week']->total)</strong><small>{{ $sales['week']->cnt }} transaksi · sejak Senin</small></article>
    <article class="stat-card"><div class="stat-icon slate">↗</div><span>Penjualan Bulan Ini</span><strong>@rupiah($sales['month']->total)</strong><small>{{ $sales['month']->cnt }} transaksi · bulan berjalan</small></article>
</div>

@if (in_array(auth()->user()->role, ['owner', 'gudang']))
<div class="two-columns">
    <div class="panel">
        <div class="panel-heading">
            <div><h3>Perlu Restock</h3><p>Produk yang mencapai stok minimum</p></div>
            <a class="text-link" href="{{ route('products.index') }}">Lihat produk →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produk</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($lowStockProducts as $p)
                    <tr>
                        <td><strong>{{ $p->name }}</strong><small class="subline">{{ $p->sku }}</small></td>
                        <td>{{ $p->stock }} {{ $p->unit }}</td>
                        <td>{{ $p->minimum_stock }}</td>
                        <td><span class="pill warning">Stok rendah</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Semua stok aman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading"><div><h3>Aktivitas Stok</h3><p>Akumulasi pergerakan barang</p></div></div>
        <p>Total barang masuk<br><strong>{{ $stats['stock_in'] }} unit</strong></p>
        <p>Total barang keluar<br><strong>{{ $stats['stock_out'] }} unit</strong></p>
        @if (auth()->user()->role === 'gudang')
            <a class="btn primary" href="{{ route('stock.index') }}">Catat pergerakan stok</a>
        @endif
    </div>
</div>
@endif
@endsection
