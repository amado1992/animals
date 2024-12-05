<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAirfreightIdToOffersPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers_airfreight_pallets', function (Blueprint $table) {
            $table->foreignId('airfreight_id')->after('offer_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers_airfreight_pallets', function (Blueprint $table) {
            $table->dropColumn('airfreight_id');
        });
    }
}
