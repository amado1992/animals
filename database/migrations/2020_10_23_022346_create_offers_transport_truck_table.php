<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTransportTruckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers_transport_truck', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('from_country')->nullable()->constrained('countries');
            $table->foreignId('to_country')->nullable()->constrained('countries');
            $table->double('total_km')->default(0);
            $table->double('cost_rate_per_km')->default(0);
            $table->double('sale_rate_per_km')->default(0);
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
        Schema::dropIfExists('offers_transport_truck');
    }
}
