<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOurwantedlistsOurwantedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ourwantedlists_ourwanted', function (Blueprint $table) {
            $table->id();
            $table->foreignId('our_wanted_list_id')->constrained('our_wanted_lists')->onDelete('cascade');
            $table->foreignId('our_wanted_id')->constrained('our_wanted')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ourwantedlists_ourwanted');
    }
}
