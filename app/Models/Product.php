<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const UNITS = [
        'Buah', 'Batang', 'Sak', 'Kg', 'Ons', 'Gram', 'Kotak', 'Dus', 'Kaleng', 'Liter',
        'Meter', 'Lembar', 'Kubik', 'Roll', 'Set', 'Pasang', 'Paket', 'Bungkus', 'Karung', 'Ikat',
    ];

    protected $fillable = [
        'category_id', 'sku', 'name', 'description', 'unit',
        'cost_price', 'price', 'stock', 'minimum_stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)->withPivot('supplier_price');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }
}
