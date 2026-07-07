<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapitalsTable extends Migration
{
    public function up()
    {
        Schema::create('capitals', function (Blueprint $table) {
            $table->id();
            $table->string('capital_no')->unique();
            $table->string('investor_name');
            $table->string('investor_phone')->nullable();
            $table->text('investor_address')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->date('capital_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['active', 'partially_paid', 'fully_paid', 'closed'])->default('active');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('capitals');
    }
}
