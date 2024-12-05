<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOffersAirfreightPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers_airfreight_pallets', function (Blueprint $table) {
            $table->enum('status', ['no_entry', 'estimated', 'quotation', 'real_costs'])->after('pallet_sale_value')->default('no_entry');
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
            $table->dropColumn('status');
        });
    }
}
