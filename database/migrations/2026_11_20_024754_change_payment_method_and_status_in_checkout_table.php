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
        Schema::table('checkouts', function (Blueprint $table) {
            // 1. Ganti nama kolom 'payment-method' (strip) menjadi 'payment_method' (underscore)
            $table->renameColumn('payment-method', 'payment_method');
        });

        Schema::table('checkouts', function (Blueprint $table) {
            // 2. Ubah tipe data 'payment_method' menjadi string nullable
            $table->string('payment_method')->nullable()->change();

            // 3. Buat kolom 'status' baru (tanpa ->change() karena belum ada)
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('payment_method', ['Free', 'Virtual Account', 'QRIS'])->change();
        });

        Schema::table('checkouts', function (Blueprint $table) {
            $table->renameColumn('payment_method', 'payment-method');
        });
    }
};
