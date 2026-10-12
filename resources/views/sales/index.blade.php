@extends('layouts.app')
@section('title', 'Riwayat Penjualan')

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nomor Transaksi</th><th>Waktu</th><th>Kasir</th><th>Total</th><th>Bayar</th><th>Kembalian</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($sales as $s)
                <tr>
                    <td><span class="code">{{ $s->invoice_number }}</span></td>
                    <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>@rupiah($s->total)</td>
                    <td>@rupiah($s->paid)</td>
                    <td>@rupiah($s->change_amount)</td>
                    <td><a class="text-link" href="{{ route('sales.show', $s) }}">Struk</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada penjualan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $sales->links() }}
</div>
@endsection
