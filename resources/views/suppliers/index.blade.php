@extends('layouts.app')
@section('title', 'Data Supplier')

@section('content')
@php($canEdit = auth()->user()->role === 'gudang')
<div class="page-intro">
    <div><p>{{ $suppliers->count() }} supplier terdaftar</p></div>
    @if ($canEdit)<a class="btn primary" href="{{ route('suppliers.create') }}">+ Tambah Supplier</a>@endif
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Telepon</th><th>Alamat</th><th>Jumlah Produk</th>@if ($canEdit)<th>Aksi</th>@endif</tr></thead>
            <tbody>
            @forelse ($suppliers as $s)
                <tr>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->address }}</td>
                    <td>{{ $s->products_count }}</td>
                    @if ($canEdit)
                    <td class="row-actions">
                        <a class="text-link" href="{{ route('suppliers.edit', $s) }}">Edit</a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf @method('DELETE')
                            <button class="link-danger">Hapus</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada supplier.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
