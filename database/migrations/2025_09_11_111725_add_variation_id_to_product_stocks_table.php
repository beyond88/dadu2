<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariationIdToProductStocksTable extends Migration
{
    public function up()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            // New column for variable product stock
            $table->foreignId('variation_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('variations')
                  ->cascadeOnDelete();

            // Optional: add index for faster queries
            $table->index(['product_id', 'variation_id']);
        });
    }

    public function down()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropForeign(['variation_id']);
            $table->dropColumn('variation_id');
        });
    }
}
