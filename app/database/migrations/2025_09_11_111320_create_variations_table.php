<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationsTable extends Migration
{
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the variation (e.g., "Red, Large")
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku');
            $table->index('sku');
            $table->decimal('price', 12, 2);
            $table->string('customer_buying_price')->nullable();
            $table->string('regular_price')->nullable();
            $table->string('barcode')->nullable();
            $table->string('barcode_image')->nullable();
             $table->text('desc')->nullable();
            $table->string('thumb')->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->string('stock_status')->nullable();
            $table->boolean('manage_stock')->nullable();
            $table->integer('weight')->nullable();
            $table->foreignId('weight_unit_id')->nullable()->constrained('weight_units', 'id')->onDelete('set null');
            $table->integer('dimension_l')->nullable();
            $table->integer('dimension_w')->nullable();
            $table->integer('dimension_d')->nullable();
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units', 'id')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('variations');
    }
}
