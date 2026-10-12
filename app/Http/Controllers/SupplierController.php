<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('products')->orderBy('name')->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validated($request));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->exists()) {
            return back()->with('error', 'Supplier masih terhubung dengan produk, tidak dapat dihapus.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'max:120'],
            'phone' => ['nullable', 'max:30'],
            'address' => ['nullable', 'max:255'],
        ]);
    }
}
