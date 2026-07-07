<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Dynamic, per-product label shown for sold-by-weight ("barrel") products
            // (e.g. "barrel", "drum", "sack"). Defaults to "Barrel".
            $table->string('barrel_label')->default('Barrel')->after('kg_per_barrel');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('barrel_label');
        });
    }
};
