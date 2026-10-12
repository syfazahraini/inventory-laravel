@extends('layouts.app')
@section('title', 'Data Produk')

@section('content')
@php($canEdit = auth()->user()->role === 'gudang')
<div class="page-intro">
    <div><p>Kelola barang, harga, satuan, dan persediaan toko.</p></div>
    <div>
        @if ($canEdit)<a class="btn primary" href="{{ route('products.create') }}">+ Tambah Produk</a>@endif
        <span class="pill neutral">{{ $products->total() }} produk</span>
    </div>
</div>

<div class="panel">
    <form class="filter-bar" method="GET" action="{{ route('products.index') }}">
        <input name="search" placeholder="Cari nama atau SKU..." value="{{ request('search') }}">
        <select name="category_id">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="sort">
            @foreach (['name' => 'Nama', 'sku' => 'SKU', 'stock' => 'Stok', 'price' => 'Harga jual'] as $k => $v)
                <option value="{{ $k }}" @selected($sort === $k)>Urut: {{ $v }}</option>
            @endforeach
        </select>
        <select name="dir">
            <option value="asc" @selected($dir === 'asc')>Naik</option>
            <option value="desc" @selected($dir === 'desc')>Turun</option>
        </select>
        <button class="btn primary">Terapkan</button>
        <a class="btn secondary" href="{{ route('products.index') }}">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th><th>Nama Produk</th><th>Kategori</th><th>Supplier</th><th>Satuan</th>
                    <th>Harga Modal</th><th>Harga Jual</th><th>Stok</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($products as $p)
                <tr>
                    <td><span class="code">{{ $p->sku }}</span></td>
                    <td>
                        <a class="text-link" href="{{ route('products.show', $p) }}"><strong>{{ $p->name }}</strong></a>
                        @if ($p->description)<small class="subline">{{ $p->description }}</small>@endif
                    </td>
                    <td>{{ $p->category->name }}</td>
                    <td>{{ $p->suppliers->pluck('name')->join(', ') ?: '-' }}</td>
                    <td>{{ $p->unit }}</td>
                    <td>@rupiah($p->cost_price)</td>
                    <td>@rupiah($p->price)</td>
                    <td>
                        <strong class="{{ $p->isLowStock() ? 'text-danger' : '' }}">{{ $p->stock }}</strong>
                        @if ($p->isLowStock())<span class="pill warning">Rendah</span>@endif
                    </td>
                    <td class="row-actions">
                        <a class="text-link" href="{{ route('products.show', $p) }}">Detail</a>
                        @if ($canEdit)
                            <a class="text-link" href="{{ route('products.edit', $p) }}">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini jika belum memiliki histori?')">
                                @csrf @method('DELETE')
                                <button class="link-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">Tidak ada produk yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
@endsection
