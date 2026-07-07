<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_no')->unique();
            $table->string('borrower_name');
            $table->string('borrower_phone')->nullable();
            $table->text('borrower_address')->nullable();
            $table->enum('loan_type', ['given', 'taken'])->default('taken')->comment('given=আমরা দিয়েছি, taken=আমরা নিয়েছি');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->date('loan_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['active', 'partially_paid', 'fully_paid', 'written_off'])->default('active');
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
        Schema::dropIfExists('loans');
    }
}
