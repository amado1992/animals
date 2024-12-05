<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersSpeciesCratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_species_crates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_species_id')->nullable()->constrained('offers_species')->onDelete('cascade');
            $table->foreignId('crate_id')->nullable()->constrained();
            $table->integer('quantity_males')->unsigned()->default(0);
            $table->integer('quantity_females')->unsigned()->default(0);
            $table->integer('quantity_unsexed')->unsigned()->default(0);
            $table->integer('quantity_pairs')->unsigned()->default(0);
            $table->integer('length')->default(0);
            $table->integer('wide')->default(0);
            $table->integer('height')->default(0);
            $table->double('cost_price')->default(0);
            $table->double('sale_price')->default(0);
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
        Schema::dropIfExists('offers_species_crates');
    }
}
