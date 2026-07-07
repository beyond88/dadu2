<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('store_name');
            $table->string('store_url');
            $table->boolean('is_webhook_enabled')->default(true);
            $table->boolean('is_connected')->default(true);
            $table->string('consumer_key')->nullable();
            $table->string('consumer_secret')->nullable();
            $table->string('access_token')->nullable();
            $table->string('access_token_secret')->nullable();
            
            $table->timestamps();

            
            // Composite unique key for store_name per type
            $table->unique(['type', 'store_name']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platforms');
    }
}
