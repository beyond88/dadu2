<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draft_invoice_items', function (Blueprint $table) {
            $table->string('unit')->default('barrel')->after('quantity');
            $table->decimal('quantity', 18, 4)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('draft_invoice_items', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->integer('quantity')->default(0)->change();
        });
    }
};
