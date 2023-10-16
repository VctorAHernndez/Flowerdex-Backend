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
        Schema::create('division_species', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('common_name')->nullable();
            $table->string('slug');
            $table->string('scientific_name');
            $table->integer('year')->nullable();
            // $table->string('bibliography');
            // $table->string('author');
            // $table->string('status'); // TODO: enum
            // $table->string('rank'); // TODO: Always species?
            // family_common_name === DivisionSpecies.DivisionGenus.DivisionFamily.common_name
            // genus_id === DivisionSpecies.division_genus.trefle_id
            $table->string('image_url')->nullable();
            $table->jsonb('synonyms'); // TODO: array of strings
            // genus === DivisionSpecies.DivisionGenus.name
            // family === DivisionSpecies.DivisionGenus.DivisionFamily.name
            $table->uuid('division_genus_id'); // TODO: nullable?
            $table->integer('trefle_id')->unique();
            $table->timestamps();

            $table->foreign('division_genus_id')->references('id')->on('division_genera');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_species');
    }
};
