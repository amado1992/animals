<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurpluslistsSurplusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surpluslists_surplus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surplus_list_id')->constrained('surplus_lists')->onDelete('cascade');
            $table->foreignId('surplus_id')->constrained('surplus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surpluslists_surplus');
    }
}
