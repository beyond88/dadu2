<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_tracking', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->unsignedBigInteger('reference_id')->nullable(); 
                $table->integer('total')->default(0);
                $table->integer('processed')->default(0);
                $table->string('status')->default('running'); // running, completed, failed
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
        Schema::dropIfExists('progress_tracking');
    }
}
