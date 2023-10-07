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
        Schema::create('flower_likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('flower_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('flower_id')->references('id')->on('flowers');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unique(['flower_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flowerlike');
    }
};
