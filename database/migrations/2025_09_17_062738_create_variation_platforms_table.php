<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variation_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id')->constrained('variations')->onDelete('cascade');
           $table->foreignId('platform_id')->nullable()->constrained('platforms')->onDelete('set null');
            $table->json('platform_data')->nullable();
            $table->unsignedBigInteger('ecommerce_id')->nullable();
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
        Schema::dropIfExists('variation_platforms');
    }
}
