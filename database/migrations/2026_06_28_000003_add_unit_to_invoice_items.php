<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Unit the line was sold in: 'barrel' (default) or 'kg' for
            // sold-by-weight products. Existing rows are barrel/piece sales.
            $table->string('unit')->default('barrel')->after('quantity');
            // Allow fractional quantities (e.g. kg sales, fractional barrels).
            $table->decimal('quantity', 18, 4)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->integer('quantity')->default(0)->change();
        });
    }
};
