<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Kolom sorting hanya dari allowlist
        $sort = in_array($request->sort, ['name', 'sku', 'stock', 'price'], true) ? $request->sort : 'name';
        $dir = $request->dir === 'desc' ? 'desc' : 'asc';

        $products = Product::with(['category', 'suppliers'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sort', 'dir'));
    }

    public function create()
    {
        return view('products.form', $this->formData(new Product(['minimum_stock' => 5, 'stock' => 0])));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules() + [
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $product = Product::create(Arr::except($data, ['suppliers']));

            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => 'IN',
                    'quantity' => $product->stock,
                    'note' => 'Stok awal',
                ]);
            }

            $this->syncSuppliers($product, $data);
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'suppliers']);
        $movements = $product->stockMovements()->with('user')->latest()->limit(10)->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product)
    {
        return view('products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->rules($product));

        DB::transaction(function () use ($data, $product) {
            // Stok tidak diubah di sini; stok hanya berubah lewat transaksi stok
            $product->update(Arr::except($data, ['suppliers']));
            $this->syncSuppliers($product, $data);
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->stockMovements()->exists() || $product->saleItems()->exists()) {
            return back()->with('error', 'Produk memiliki histori. Jangan hapus histori; biarkan produk tetap tersimpan.');
        }

        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function rules(?Product $product = null): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'max:50', Rule::unique('products', 'sku')->ignore($product)],
            'name' => ['required', 'max:150'],
            'description' => ['nullable', 'max:255'],
            'unit' => ['required', Rule::in(Product::UNITS)],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['exists:suppliers,id'],
        ];
    }

    private function syncSuppliers(Product $product, array $data): void
    {
        $sync = collect($data['suppliers'] ?? [])
            ->mapWithKeys(fn ($id) => [$id => ['supplier_price' => $data['cost_price']]])
            ->all();

        $product->suppliers()->sync($sync);
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ];
    }
}
