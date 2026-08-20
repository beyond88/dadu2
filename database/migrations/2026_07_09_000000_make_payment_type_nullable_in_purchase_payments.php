<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->string('payment_type', 100)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->string('payment_type', 100)->nullable(false)->change();
        });
    }
};
