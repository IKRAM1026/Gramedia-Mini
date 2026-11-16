<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicatePairs = DB::table('subscription_package_books')
            ->select('subscription_package_id', 'book_id')
            ->selectRaw('MIN(id) as keep_id')
            ->groupBy('subscription_package_id', 'book_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicatePairs as $duplicatePair) {
            DB::table('subscription_package_books')
                ->where('subscription_package_id', $duplicatePair->subscription_package_id)
                ->where('book_id', $duplicatePair->book_id)
                ->where('id', '!=', $duplicatePair->keep_id)
                ->delete();
        }

        Schema::table('subscription_package_books', function (Blueprint $table) {
            $table->unique(['subscription_package_id', 'book_id'], 'subscription_package_book_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_package_books', function (Blueprint $table) {
            $table->dropUnique('subscription_package_book_unique');
        });
    }
};
