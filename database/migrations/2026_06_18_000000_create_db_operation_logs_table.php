<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('db_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');                 // export | import
            $table->string('status');                 // success | failed
            $table->string('file_name')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['action', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('db_operation_logs');
    }
};
