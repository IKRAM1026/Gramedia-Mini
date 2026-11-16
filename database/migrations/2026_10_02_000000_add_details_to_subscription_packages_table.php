<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->string('nama_package')->default('');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('color', 50)->default('');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn(['nama_package', 'description', 'price', 'color']);
        });
    }
};