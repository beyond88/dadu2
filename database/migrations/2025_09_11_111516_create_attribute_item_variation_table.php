<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeItemVariationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_item_variation', function (Blueprint $table) {
            // Foreign key to variations table
            $table->foreignId('variation_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Foreign key to attribute_items table
            $table->foreignId('attribute_item_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Composite primary key
            $table->primary(['variation_id', 'attribute_item_id']);

            // Optional: timestamps if you want to track when a pivot was created
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
        Schema::dropIfExists('attribute_item_variation');
    }
}
