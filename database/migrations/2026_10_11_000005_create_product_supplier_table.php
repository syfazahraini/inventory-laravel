<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('supplier_price', 14, 2)->default(0);
            $table->primary(['product_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
