<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product-level barrel (sold-by-weight) configuration.
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_weight_based')->default(false)->after('split_sale');
            $table->decimal('kg_per_barrel', 10, 2)->nullable()->after('is_weight_based');
        });

        // Allow fractional pieces (barrels) throughout the purchase -> stock path.
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 15, 2)->default(0)->change();
        });
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->default(0)->change();
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->default(0)->change();
        });
        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_weight_based', 'kg_per_barrel']);
            $table->integer('stock')->default(0)->change();
        });
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->change();
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->change();
        });
        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->change();
        });
    }
};
