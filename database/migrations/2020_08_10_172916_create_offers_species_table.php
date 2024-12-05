<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersSpeciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('oursurplus_id')->nullable()->constrained('our_surplus');
            $table->integer('offerQuantityM')->unsigned()->default(0);
            $table->integer('offerQuantityF')->unsigned()->default(0);
            $table->integer('offerQuantityU')->unsigned()->default(0);
            $table->double('offerCostPriceM')->default(0);
            $table->double('offerCostPriceF')->default(0);
            $table->double('offerCostPriceU')->default(0);
            $table->double('offerCostPriceP')->default(0);
            $table->double('offerSalePriceM')->default(0);
            $table->double('offerSalePriceF')->default(0);
            $table->double('offerSalePriceU')->default(0);
            $table->double('offerSalePriceP')->default(0);
            $table->text('client_remarks')->nullable();
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
        Schema::dropIfExists('offers_species');
    }
}
