<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Login / logout
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Gudang: kelola data (didaftarkan lebih dulu agar /products/create tidak tertangkap route show)
    Route::middleware(RoleMiddleware::class . ':gudang')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::resource('suppliers', SupplierController::class)->except(['index', 'show']);
        Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    });

    // Owner dan Gudang: lihat data
    Route::middleware(RoleMiddleware::class . ':owner,gudang')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::resource('categories', CategoryController::class)->only('index');
        Route::resource('suppliers', SupplierController::class)->only('index');
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    });

    // Kasir: penjualan
    Route::middleware(RoleMiddleware::class . ':kasir')->group(function () {
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // Owner dan Kasir: riwayat penjualan
    Route::middleware(RoleMiddleware::class . ':owner,kasir')->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });
});
