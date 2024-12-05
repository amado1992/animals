<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOursurpluslistsOursurplusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oursurpluslists_oursurplus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_surplus_list_id')->constrained('our_surplus_lists')->onDelete('cascade');
            $table->foreignId('our_surplus_id')->constrained('our_surplus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oursurpluslists_oursurplus');
    }
}
