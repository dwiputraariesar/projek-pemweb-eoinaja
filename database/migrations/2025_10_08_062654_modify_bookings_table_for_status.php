<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita akan menggunakan Schema::table untuk mengubah tabel yang sudah ada
        Schema::table('bookings', function (Blueprint $table) {
            // Mengubah kolom status agar bisa menampung nilai yang berbeda
            // dan defaultnya adalah 'pending' saat booking pertama kali dibuat.
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika di-rollback
            $table->string('status')->default('paid')->change();
        });
    }
};