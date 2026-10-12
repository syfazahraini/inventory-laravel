<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 40)->nullable()->unique()->after('name');
            $table->enum('role', ['owner', 'gudang', 'kasir'])->default('kasir')->after('password');
            $table->string('phone', 30)->nullable()->after('role');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'phone', 'status']);
        });
    }
};
