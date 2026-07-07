<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRelationsTable extends Migration
{
    public function up()
    {
        Schema::create('product_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('related_product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['parent_product_id', 'related_product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_relations');
    }
}
