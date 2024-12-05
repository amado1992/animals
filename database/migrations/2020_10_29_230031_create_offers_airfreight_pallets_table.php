<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersAirfreightPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_airfreight_pallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('pallet_quantity')->unsigned()->default(0);
            $table->foreignId('departure_continent')->constrained('regions');
            $table->foreignId('arrival_continent')->constrained('regions');
            $table->double('pallet_cost_value')->default(0);
            $table->double('pallet_sale_value')->default(0);
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
        Schema::dropIfExists('offers_airfreight_pallets');
    }
}
