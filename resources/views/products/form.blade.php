@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
@php($selectedSuppliers = old('suppliers', $product->exists ? $product->suppliers->pluck('id')->all() : []))
<div class="panel form-panel">
    <form method="POST" class="form-grid"
          action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <label>SKU / Kode Barang
            <input name="sku" value="{{ old('sku', $product->sku) }}" maxlength="50" required>
            @error('sku')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Nama Produk
            <input name="name" value="{{ old('name', $product->name) }}" maxlength="150" required>
            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label class="span-2">Deskripsi Produk
            <input name="description" value="{{ old('description', $product->description) }}" maxlength="255" placeholder="Contoh: Semen 50 kg, cocok untuk pondasi dan cor">
            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Kategori
            <select name="category_id" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((string) old('category_id', $product->category_id) === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('category_id')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Satuan
            <select name="unit" required>
                <option value="">Pilih satuan</option>
                @foreach (\App\Models\Product::UNITS as $unit)
                    <option value="{{ $unit }}" @selected(old('unit', $product->unit) === $unit)>{{ $unit }}</option>
                @endforeach
            </select>
            @error('unit')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Harga Modal (Rp)
            <input type="number" min="0" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required>
            @error('cost_price')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Harga Jual (Rp)
            <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $product->price) }}" required>
            @error('price')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        @if (! $product->exists)
            <label>Stok Awal
                <input type="number" min="0" step="1" name="stock" value="{{ old('stock', 0) }}" required>
                @error('stock')<small class="text-danger">{{ $message }}</small>@enderror
            </label>
        @else
            <label>Stok Saat Ini
                <input value="{{ $product->stock }} {{ $product->unit }}" disabled>
                <small>Gunakan menu Stok masuk/keluar untuk mengubah stok.</small>
            </label>
        @endif

        <label>Stok Minimum
            <input type="number" min="0" step="1" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}" required>
            @error('minimum_stock')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <div class="span-2">
            <strong style="font-size:12px;color:#4d6271">Supplier (boleh lebih dari satu)</strong>
            <div style="display:flex;flex-wrap:wrap;gap:8px 20px;margin-top:8px">
                @forelse ($suppliers as $s)
                    <label style="flex-direction:row;align-items:center;gap:6px;font-weight:400">
                        <input type="checkbox" style="width:auto" name="suppliers[]" value="{{ $s->id }}" @checked(in_array($s->id, $selectedSuppliers))>
                        {{ $s->name }}
                    </label>
                @empty
                    <small>Belum ada supplier. Tambahkan di menu Supplier.</small>
                @endforelse
            </div>
        </div>

        <div class="form-actions">
            <button class="btn primary">{{ $product->exists ? 'Simpan Perubahan' : 'Tambah Produk' }}</button>
            <a class="btn secondary" href="{{ route('products.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
