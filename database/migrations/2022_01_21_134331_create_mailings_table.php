<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->id();
            $table->text('subject')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->timestamp('date_sent_out')->nullable();
            $table->string('language')->nullable();
            $table->string('institution_level', 5)->nullable();
            $table->text('institution_type')->nullable();
            $table->text('part_of_world')->nullable();
            $table->text('exclude_continents')->nullable();
            $table->text('exclude_countries')->nullable();
            $table->text('remarks')->nullable();
            $table->string('mailing_template', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailings');
    }
}
