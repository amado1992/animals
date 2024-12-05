<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOffersSpeciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers_species', function (Blueprint $table) {
            $table->enum('status', ['no_entry', 'estimated', 'quotation', 'real_costs'])->after('offerSalePriceP')->default('no_entry');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers_species', function (Blueprint $table) {
            //
        });
    }
}
