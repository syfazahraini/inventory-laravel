@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="panel form-panel">
    <form method="POST" class="form-grid"
          action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif

        <label>Nama Supplier
            <input name="name" value="{{ old('name', $supplier->name) }}" maxlength="120" required>
            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
        </label>
        <label>Telepon
            <input name="phone" value="{{ old('phone', $supplier->phone) }}" maxlength="30">
            @error('phone')<small class="text-danger">{{ $message }}</small>@enderror
        </label>
        <label class="span-2">Alamat
            <input name="address" value="{{ old('address', $supplier->address) }}" maxlength="255">
            @error('address')<small class="text-danger">{{ $message }}</small>@enderror
        </label>

        <div class="form-actions">
            <button class="btn primary">{{ $supplier->exists ? 'Simpan Perubahan' : 'Tambah Supplier' }}</button>
            <a class="btn secondary" href="{{ route('suppliers.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
