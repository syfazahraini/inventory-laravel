@extends('layouts.app')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="panel form-panel">
    <form method="POST" class="form-grid"
          action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <label class="span-2">Nama Kategori
            <input name="name" value="{{ old('name', $category->name) }}" maxlength="80" required placeholder="Contoh: Semen">
            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <div class="form-actions">
            <button class="btn primary">{{ $category->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
            <a class="btn secondary" href="{{ route('categories.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
