<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sold-by-weight ("barrel") products are stocked in barrels but can now be
     * sold in kg (kg / kg_per_barrel = fractional barrels). Bump the decimal
     * precision so fractional-barrel decrements don't lose accuracy for
     * conversion factors that don't divide evenly into 2 decimals.
     */
    public function up(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('quantity', 18, 4)->default(0)->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 18, 4)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->default(0)->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 15, 2)->default(0)->change();
        });
    }
};
