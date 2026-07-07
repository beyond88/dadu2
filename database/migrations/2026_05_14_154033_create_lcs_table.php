<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('dollar_price', 15, 2)->default(0);
            $table->decimal('usd_rate', 10, 4)->default(0);
            $table->decimal('lc_amount_bdt', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('final_cost', 15, 2)->default(0);
            $table->decimal('per_dollar_cost', 10, 4)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('lc_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lc_id')->constrained('lcs')->onDelete('cascade');
            $table->string('expense_name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index('lc_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_expenses');
        Schema::dropIfExists('lcs');
    }
};
