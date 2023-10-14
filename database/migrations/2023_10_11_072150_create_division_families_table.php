<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('division_families', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('common_name')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->integer('trefle_id')->unique();
            $table->uuid('division_order_id')->nullable();
            $table->timestamps();
            
            $table->foreign('division_order_id')->references('id')->on('division_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_families');
    }
};
