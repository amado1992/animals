<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOurWantedAreaRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ourwanted_arearegion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_wanted_id')->constrained('our_wanted')->onDelete('cascade');
            $table->foreignId('area_region_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ourwanted_arearegion');
    }
}
