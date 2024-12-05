<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldToOfferSpecies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offers_species', function (Blueprint $table) {
            $table->enum('origin', ['cb', 'wc', 'cb_wc', 'range', 'unknown'])->nullable();
            $table->foreignId('region_id')->nullable()->constrained();
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
            $table->dropColumn('origin');
            $table->dropColumn('region_id');
        });
    }
}
