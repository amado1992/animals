<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOurSurplusAreaRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oursurplus_arearegion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_surplus_id')->constrained('our_surplus')->onDelete('cascade');
            $table->foreignId('area_region_id')->constrained('area_regions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oursurplus_arearegion');
    }
}
