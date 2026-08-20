<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An LC now holds several named amounts (like its expenses do) that share a
     * single USD rate. lcs.dollar_price keeps the total of these rows.
     */
    public function up(): void
    {
        Schema::create('lc_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lc_id')->constrained('lcs')->onDelete('cascade');
            $table->string('name');
            $table->decimal('dollar_price', 15, 2)->default(0);
            $table->timestamps();

            $table->index('lc_id');
        });

        // Carry existing single-LC rows over so nothing loses its breakdown.
        $existing = DB::table('lcs')->select('id', 'name', 'dollar_price', 'created_at', 'updated_at')->get();

        foreach ($existing as $lc) {
            DB::table('lc_items')->insert([
                'lc_id'        => $lc->id,
                'name'         => $lc->name,
                'dollar_price' => $lc->dollar_price,
                'created_at'   => $lc->created_at,
                'updated_at'   => $lc->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_items');
    }
};
