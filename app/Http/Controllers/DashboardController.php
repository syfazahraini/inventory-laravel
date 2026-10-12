<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'products' => Product::count(),
            'inventory_value' => Product::sum(DB::raw('stock * cost_price')),
            'low_stock' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'stock_in' => StockMovement::where('type', 'IN')->sum('quantity'),
            'stock_out' => StockMovement::where('type', 'OUT')->sum('quantity'),
        ];

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $summary = fn ($since) => Sale::where('status', 'selesai')
            ->where('created_at', '>=', $since)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as cnt')
            ->first();

        $sales = [
            'today' => $summary(now()->startOfDay()),
            'week' => $summary(now()->startOfWeek()),
            'month' => $summary(now()->startOfMonth()),
        ];

        return view('dashboard', compact('stats', 'lowStockProducts', 'sales'));
    }
}
