@extends('layouts.app')
@section('title', 'Kasir / Penjualan')

@section('content')
<style>
.pos-search{width:100%;max-width:380px;margin-bottom:14px;border:1px solid #dfe7ed;border-radius:8px;padding:11px 12px;font:400 13px 'DM Sans',sans-serif}
tr.picked{background:#f1f8fc}
.pos-summary{position:sticky;bottom:0;z-index:3;background:#fff;border:1px solid #dfe7ed;border-radius:12px;padding:16px 18px;margin-top:16px;box-shadow:0 -6px 18px rgba(20,40,60,.08)}
.pos-summary .row{display:flex;justify-content:space-between;align-items:baseline;gap:12px;margin-bottom:8px}
.pos-summary .total{font:800 26px Manrope,sans-serif;color:#1e3d4f}
.pos-summary .change{font:800 18px Manrope,sans-serif}
.pos-summary .change.ok{color:#387a4d}
.pos-summary .change.bad{color:#a74340}
.pos-summary label{display:flex;flex-direction:column;gap:7px;font-weight:700;font-size:12px;color:#4d6271;margin:12px 0 8px}
.pos-summary input[type=number]{width:100%;border:1px solid #dfe7ed;border-radius:8px;padding:11px 12px;font:400 15px 'DM Sans',sans-serif}
.quick-pay{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
.quick-pay button{border:1px solid #dfe7ed;background:#f6f9fb;border-radius:8px;padding:8px 12px;font:700 12px 'DM Sans',sans-serif;cursor:pointer}
.pos-summary .btn[disabled]{opacity:.45;cursor:not-allowed}
</style>

<div class="panel">
    <div class="panel-heading">
        <div>
            <h3>Produk tersedia</h3>
            <p>Centang barang, atur jumlah, lalu lihat total di bagian bawah.</p>
        </div>
    </div>

    @error('selected')<x-alert type="error">{{ $message }}</x-alert>@enderror

    <input type="search" id="cari" class="pos-search" placeholder="Cari nama produk atau SKU (pilihan tidak hilang)">

    <form method="POST" action="{{ route('sales.store') }}" id="form-kasir">
        @csrf
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Pilih</th><th>SKU</th><th>Produk</th><th>Harga</th><th>Stok</th><th>Jumlah</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                @forelse ($products as $p)
                    <tr data-row data-harga="{{ (float) $p->price }}" data-stok="{{ $p->stock }}" data-cari="{{ strtolower($p->name . ' ' . $p->sku) }}">
                        <td><input type="checkbox" class="pick" name="selected[]" value="{{ $p->id }}"></td>
                        <td>{{ $p->sku }}</td>
                        <td><strong>{{ $p->name }}</strong></td>
                        <td>@rupiah($p->price)</td>
                        <td>{{ $p->stock }} {{ $p->unit }}</td>
                        <td><input class="qty-input" type="number" min="1" max="{{ $p->stock }}" name="qty[{{ $p->id }}]" value="1"></td>
                        <td class="sub">Rp 0</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada produk dengan stok tersedia.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pos-summary">
            <div class="row"><span>Total belanja (<span id="jml-item">0</span> jenis barang)</span><span class="total" id="total">Rp 0</span></div>

            <label>Uang Dibayar (Rp)
                <input type="number" name="paid" id="bayar" min="0" step="1" required placeholder="Isi sesuai uang dari pelanggan" value="{{ old('paid') }}">
            </label>
            @error('paid')<small class="text-danger">{{ $message }}</small>@enderror
            <div class="quick-pay" id="quick-pay"></div>

            <div class="row"><span id="change-label">Kembalian</span><span class="change" id="change">Rp 0</span></div>

            <button type="submit" class="btn primary wide" id="btn-proses" disabled>Proses Penjualan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
@verbatim
<script>
(function () {
  var rows   = Array.prototype.slice.call(document.querySelectorAll('tr[data-row]'));
  var elTot  = document.getElementById('total');
  var elJml  = document.getElementById('jml-item');
  var elPay  = document.getElementById('bayar');
  var elChg  = document.getElementById('change');
  var elLbl  = document.getElementById('change-label');
  var elBtn  = document.getElementById('btn-proses');
  var elQuick= document.getElementById('quick-pay');
  var elForm = document.getElementById('form-kasir');
  var total  = 0;

  function rp(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

  function hitung() {
    var sum = 0, count = 0;
    rows.forEach(function (r) {
      var cb = r.querySelector('.pick'), qi = r.querySelector('.qty-input'), max = parseInt(r.dataset.stok, 10);
      var n = parseInt(qi.value, 10) || 0;
      if (n > max) { n = max; qi.value = max; }
      if (cb.checked && n > 0) {
        var sub = n * parseFloat(r.dataset.harga);
        sum += sub; count++;
        r.classList.add('picked');
        r.querySelector('.sub').textContent = rp(sub);
      } else {
        r.classList.remove('picked');
        r.querySelector('.sub').textContent = 'Rp 0';
      }
    });
    total = sum;
    elTot.textContent = rp(total);
    elJml.textContent = count;
    tombolCepat();
    bayar();
  }

  function tombolCepat() {
    elQuick.innerHTML = '';
    if (total <= 0) return;
    var opsi = [total];
    [1000, 5000, 10000, 50000, 100000].forEach(function (k) {
      var v = Math.ceil(total / k) * k;
      if (opsi.indexOf(v) === -1) opsi.push(v);
    });
    opsi.slice(0, 5).forEach(function (v, i) {
      var b = document.createElement('button');
      b.type = 'button';
      b.textContent = (i === 0 ? 'Uang pas ' : '') + rp(v);
      b.onclick = function () { elPay.value = v; bayar(); };
      elQuick.appendChild(b);
    });
  }

  function bayar() {
    var pay = parseInt(elPay.value, 10);
    var ok  = total > 0 && !isNaN(pay) && pay >= total;
    if (isNaN(pay) || total <= 0) {
      elLbl.textContent = 'Kembalian'; elChg.textContent = 'Rp 0'; elChg.className = 'change';
    } else if (pay < total) {
      elLbl.textContent = 'Kurang'; elChg.textContent = rp(total - pay); elChg.className = 'change bad';
    } else {
      elLbl.textContent = 'Kembalian'; elChg.textContent = rp(pay - total); elChg.className = 'change ok';
    }
    elBtn.disabled = !ok;
  }

  rows.forEach(function (r) {
    var cb = r.querySelector('.pick'), qi = r.querySelector('.qty-input');
    cb.addEventListener('change', hitung);
    qi.addEventListener('input', function () { if (parseInt(qi.value, 10) > 0) cb.checked = true; hitung(); });
  });
  elPay.addEventListener('input', bayar);

  document.getElementById('cari').addEventListener('input', function () {
    var k = this.value.toLowerCase().trim();
    rows.forEach(function (r) { r.style.display = r.dataset.cari.indexOf(k) === -1 ? 'none' : ''; });
  });

  elForm.addEventListener('submit', function (ev) {
    var pay = parseInt(elPay.value, 10);
    if (pay - total > total && !confirm('Kembalian (' + rp(pay - total) + ') lebih besar dari total belanja. Yakin nominal uang sudah benar?')) {
      ev.preventDefault();
    }
  });

  hitung();
})();
</script>
@endverbatim
@endpush
