<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sold-by-weight ("barrel") products now store the FINAL selling price
 * (entered per-kg price x kg_per_barrel) directly in `products.price`.
 *
 * Until now `price` held the RAW per-kg value, so back-fill existing barrel
 * products ONCE by multiplying their stored price by kg_per_barrel. Migrations
 * run a single time, so this cannot compound.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('is_weight_based', 1)
            ->where('kg_per_barrel', '>', 0)
            ->update(['price' => DB::raw('ROUND(price * kg_per_barrel, 4)')]);
    }

    public function down(): void
    {
        // Reverse: turn the final price back into the raw per-kg value.
        DB::table('products')
            ->where('is_weight_based', 1)
            ->where('kg_per_barrel', '>', 0)
            ->update(['price' => DB::raw('ROUND(price / kg_per_barrel, 4)')]);
    }
};
