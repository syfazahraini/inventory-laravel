@extends('layouts.app')
@section('title', 'Data Kategori')

@section('content')
@php($canEdit = auth()->user()->role === 'gudang')
<div class="page-intro">
    <div><p>{{ $categories->count() }} kategori terdaftar</p></div>
    @if ($canEdit)<a class="btn primary" href="{{ route('categories.create') }}">+ Tambah Kategori</a>@endif
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Jumlah Produk</th>@if ($canEdit)<th>Aksi</th>@endif</tr></thead>
            <tbody>
            @forelse ($categories as $c)
                <tr>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>{{ $c->products_count }}</td>
                    @if ($canEdit)
                    <td class="row-actions">
                        <a class="text-link" href="{{ route('categories.edit', $c) }}">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="link-danger">Hapus</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="3" class="empty">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
