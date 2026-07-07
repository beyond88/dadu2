<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDamageLostQtyToSaleReturnItemsTable extends Migration
{
    public function up()
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->unsignedInteger('damage_qty')->default(0)->after('return_qty');
            $table->unsignedInteger('lost_qty')->default(0)->after('damage_qty');
        });
    }

    public function down()
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->dropColumn(['damage_qty', 'lost_qty']);
        });
    }
}
