@extends('layouts.app')
@section('title', 'Struk Penjualan')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <div>
            <h3>{{ $sale->invoice_number }}</h3>
            <p>{{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->user->name }}</p>
        </div>
        <div>
            <button class="btn secondary" onclick="window.print()">Cetak</button>
            <a class="btn secondary" href="{{ route('sales.index') }}">← Riwayat</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
            <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>@rupiah($item->price)</td>
                    <td>{{ $item->quantity }}</td>
                    <td>@rupiah($item->subtotal)</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="3">Total</th><th>@rupiah($sale->total)</th></tr>
                <tr><th colspan="3">Bayar</th><th>@rupiah($sale->paid)</th></tr>
                <tr><th colspan="3">Kembalian</th><th>@rupiah($sale->change_amount)</th></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
