<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get(['id', 'sku', 'name', 'stock', 'unit']);
        $movements = StockMovement::with(['product', 'user'])->latest()->paginate(10);

        return view('stock.index', compact('products', 'movements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:IN,OUT,ADJUSTMENT'],
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'max:255'],
        ]);

        if ($data['type'] !== 'ADJUSTMENT' && (int) $data['quantity'] < 1) {
            throw ValidationException::withMessages(['quantity' => 'Jumlah harus minimal 1.']);
        }

        DB::transaction(function () use ($data, $request) {
            // Kunci baris produk agar dua transaksi bersamaan tidak membuat stok salah
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $qty = (int) $data['quantity'];
            $note = $data['note'] ?? null;

            if ($data['type'] === 'IN') {
                $product->stock += $qty;
            } elseif ($data['type'] === 'OUT') {
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock}",
                    ]);
                }
                $product->stock -= $qty;
            } else {
                // Koreksi: jumlah yang diisi adalah stok baru hasil hitung fisik
                $diff = abs($product->stock - $qty);
                if ($diff === 0) {
                    throw ValidationException::withMessages(['quantity' => 'Stok baru sama dengan stok saat ini.']);
                }
                $note = "Koreksi stok {$product->stock} menjadi {$qty}. " . $note;
                $product->stock = $qty;
                $qty = $diff;
            }

            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id,
                'type' => $data['type'],
                'quantity' => $qty,
                'note' => trim((string) $note),
            ]);
        });

        return redirect()->route('stock.index')->with('success', 'Transaksi stok berhasil dicatat.');
    }
}
