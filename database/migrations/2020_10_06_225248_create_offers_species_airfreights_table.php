<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersSpeciesAirfreightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_species_airfreights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_species_id')->nullable()->constrained('offers_species')->onDelete('cascade');
            $table->foreignId('airfreight_id')->nullable()->constrained();
            $table->double('cost_volKg')->default(0);
            $table->double('sale_volKg')->default(0);
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
        Schema::dropIfExists('offers_species_airfreights');
    }
}
