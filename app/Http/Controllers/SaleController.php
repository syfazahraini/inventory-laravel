<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with('user')
            ->when($request->user()->role === 'kasir', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(10);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'selected' => ['required', 'array', 'min:1', 'max:50'],
            'selected.*' => ['integer', 'exists:products,id'],
            'qty' => ['required', 'array'],
            'paid' => ['required', 'integer', 'min:0'],
        ], [
            'selected.required' => 'Pilih minimal satu produk.',
            'selected.min' => 'Pilih minimal satu produk.',
        ]);

        $saleId = DB::transaction(function () use ($request) {
            $items = [];
            $total = 0;

            foreach (array_unique($request->input('selected')) as $productId) {
                $qty = filter_var($request->input("qty.$productId"), FILTER_VALIDATE_INT);
                if ($qty === false || $qty < 1) {
                    throw ValidationException::withMessages(['selected' => 'Jumlah barang harus bilangan bulat positif.']);
                }

                $product = Product::lockForUpdate()->findOrFail($productId);
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'selected' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock}",
                    ]);
                }

                $subtotal = $qty * (float) $product->price;
                $total += $subtotal;
                $items[] = [$product, $qty, $subtotal];
            }

            $paid = (int) $request->input('paid');
            if ($paid < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Pembayaran kurang dari total belanja (Rp ' . number_format($total, 0, ',', '.') . ').',
                ]);
            }

            $sale = Sale::create([
                'invoice_number' => 'SB' . date('ymdHis') . random_int(100, 999),
                'user_id' => $request->user()->id,
                'total' => $total,
                'paid' => $paid,
                'change_amount' => $paid - $total,
                'status' => 'selesai',
            ]);

            foreach ($items as [$product, $qty, $subtotal]) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'cost_price' => $product->cost_price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => 'OUT',
                    'quantity' => $qty,
                    'note' => 'Penjualan ' . $sale->invoice_number,
                    'sale_id' => $sale->id,
                ]);
            }

            return $sale->id;
        });

        return redirect()->route('sales.show', $saleId)->with('success', 'Penjualan berhasil diproses.');
    }

    public function show(Request $request, Sale $sale)
    {
        // Kasir hanya boleh melihat transaksinya sendiri
        if ($request->user()->role === 'kasir' && $sale->user_id !== $request->user()->id) {
            abort(403, '403 - Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        $sale->load(['items.product', 'user']);

        return view('sales.show', compact('sale'));
    }
}
