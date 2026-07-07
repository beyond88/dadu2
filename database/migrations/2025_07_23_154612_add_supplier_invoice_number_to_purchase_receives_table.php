<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierInvoiceNumberToPurchaseReceivesTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_receives', function (Blueprint $table) {
            $table->string('supplier_invoice_number')->nullable()->after('purchase_id');
        });
    }

    public function down()
    {
        Schema::table('purchase_receives', function (Blueprint $table) {
            $table->dropColumn('supplier_invoice_number');
        });
    }
}
