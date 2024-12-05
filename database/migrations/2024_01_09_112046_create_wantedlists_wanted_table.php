<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWantedlistsWantedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wantedlists_wanted', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wanted_list_id')->constrained('wanted_lists')->onDelete('cascade');
            $table->foreignId('wanted_id')->constrained('wanted')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wantedlists_wanted');
    }
}
