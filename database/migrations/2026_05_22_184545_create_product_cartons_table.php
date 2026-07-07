<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCartonsTable extends Migration
{
    public function up()
    {
        Schema::create('product_cartons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->onDelete('cascade');
            $table->foreignId('carton_product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('qty_per_carton')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_cartons');
    }
}
