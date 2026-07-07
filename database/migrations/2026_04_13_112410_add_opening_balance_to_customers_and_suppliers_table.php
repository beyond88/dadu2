<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpeningBalanceToCustomersAndSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('opening_balance', 16, 2)->default(0)->after('status');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('opening_balance', 16, 2)->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('opening_balance');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('opening_balance');
        });
    }
}
