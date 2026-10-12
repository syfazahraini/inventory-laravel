@extends('layouts.app')
@section('title', 'Stok Masuk / Keluar')

@section('content')
@if (auth()->user()->role === 'gudang')
<div class="panel form-panel">
    <div class="panel-heading">
        <div><h3>Catat Pergerakan Stok</h3><p>Barang masuk, barang keluar, atau koreksi hasil hitung fisik.</p></div>
    </div>
    <form method="POST" action="{{ route('stock.store') }}" class="form-grid">
        @csrf
        <label>Produk
            <select name="product_id" required>
                <option value="">Pilih produk</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected((string) old('product_id') === (string) $p->id)>{{ $p->name }} ({{ $p->sku }}) · stok {{ $p->stock }} {{ $p->unit }}</option>
                @endforeach
            </select>
            @error('product_id')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Jenis Transaksi
            <select name="type" required>
                <option value="IN" @selected(old('type') === 'IN')>Barang masuk</option>
                <option value="OUT" @selected(old('type') === 'OUT')>Barang keluar</option>
                <option value="ADJUSTMENT" @selected(old('type') === 'ADJUSTMENT')>Koreksi (isi stok baru hasil hitung fisik)</option>
            </select>
            @error('type')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Jumlah
            <input type="number" name="quantity" min="0" step="1" value="{{ old('quantity') }}" required>
            @error('quantity')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <label>Keterangan
            <input name="note" maxlength="255" value="{{ old('note') }}" placeholder="Contoh: Pembelian dari supplier">
            @error('note')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <div class="form-actions"><button class="btn primary">Simpan Transaksi</button></div>
    </form>
</div>
@else
<div class="alert info">Mode lihat saja. Transaksi stok hanya dapat dicatat oleh Gudang.</div>
@endif

<div class="panel">
    <div class="panel-heading"><div><h3>Riwayat Stok</h3><p>Seluruh pergerakan barang, terbaru di atas</p></div></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Produk</th><th>Tipe</th><th>Jumlah</th><th>Oleh</th><th>Keterangan</th></tr></thead>
            <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $m->product->name }}</strong></td>
                    <td><span class="pill {{ $m->type === 'IN' ? 'success' : 'neutral' }}">{{ $m->type }}</span></td>
                    <td>{{ $m->quantity }}</td>
                    <td>{{ $m->user->name }}</td>
                    <td>{{ $m->note }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada riwayat stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $movements->links() }}
</div>
@endsection
