@extends('layouts.app')
@section('title', 'Detail Produk')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <div>
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->sku }} · {{ $product->category->name }}</p>
        </div>
        <a class="btn secondary" href="{{ route('products.index') }}">← Kembali</a>
    </div>
    <p>{{ $product->description ?: 'Belum ada deskripsi.' }}</p>
    <div class="table-wrap">
        <table>
            <tbody>
                <tr><th>Satuan</th><td>{{ $product->unit }}</td></tr>
                <tr><th>Harga Modal</th><td>@rupiah($product->cost_price)</td></tr>
                <tr><th>Harga Jual</th><td>@rupiah($product->price)</td></tr>
                <tr><th>Stok</th><td>{{ $product->stock }} (minimum {{ $product->minimum_stock }})</td></tr>
                <tr><th>Supplier</th><td>{{ $product->suppliers->pluck('name')->join(', ') ?: '-' }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-heading"><div><h3>10 Pergerakan Stok Terakhir</h3></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Tipe</th><th>Jumlah</th><th>Oleh</th><th>Keterangan</th></tr></thead>
            <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="pill neutral">{{ $m->type }}</span></td>
                    <td>{{ $m->quantity }}</td>
                    <td>{{ $m->user->name }}</td>
                    <td>{{ $m->note }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada pergerakan stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
