<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariationIdToInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add variation_id column after product_id
            $table->unsignedBigInteger('variation_id')->nullable()->after('product_id');
            
            // Add foreign key constraint
            $table->foreign('variation_id')
                ->references('id')
                ->on('variations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['variation_id']);
            
            // Drop the column
            $table->dropColumn('variation_id');
        });
    }
}
