<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->unsignedInteger('quantity');
            $table->string('note', 255)->nullable();
            $table->foreignId('sale_id')->nullable()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
