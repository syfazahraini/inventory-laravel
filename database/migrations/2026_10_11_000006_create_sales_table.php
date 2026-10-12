<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 40)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('total', 14, 2);
            $table->decimal('paid', 14, 2);
            $table->decimal('change_amount', 14, 2);
            $table->enum('status', ['selesai', 'dibatalkan'])->default('selesai');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
