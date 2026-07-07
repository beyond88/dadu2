<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFreeToInvoiceItemsAndStockHistories extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('sub_total');
        });

        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('action_from');
            $table->string('note')->nullable()->after('is_free');
        });
    }

    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->dropColumn(['is_free', 'note']);
        });
    }
}
