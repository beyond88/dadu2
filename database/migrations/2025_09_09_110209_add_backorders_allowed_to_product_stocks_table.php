<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->boolean('backorders_allowed')
                  ->default(false)
                  ->after('quantity');

            $table->boolean('manage_stock')
                  ->default(false) 
                  ->after('backorders_allowed');
        });
    }

    public function down()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn(['backorders_allowed', 'manage_stock']);
        });
    }
};
