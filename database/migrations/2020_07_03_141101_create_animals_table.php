<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->bigInteger('code_number')->nullable()->unique();
            $table->string('common_name', 255)->nullable();
            $table->string('common_name_alt', 255)->nullable();
            $table->string('scientific_name', 255)->nullable();
            $table->string('scientific_name_slug', 191)->nullable()->unique();
            $table->string('scientific_name_alt', 255)->nullable();
            $table->string('spanish_name', 255)->nullable();
            $table->string('cites_global_key', 3)->nullable();
            $table->string('cites_europe_key', 3)->nullable();
            $table->foreignId('genus_id')->nullable()->constrained('classifications');
            $table->integer('iata_code')->nullable();
            $table->string('iata_code_letter', 1)->nullable();
            $table->float('body_weight')->nullable();
            $table->string('catalog_pic', 255)->nullable();
            $table->timestamps();

            $table->foreign('cites_global_key')
                ->references('key')
                ->on('cites');

            $table->foreign('cites_europe_key')
                ->references('key')
                ->on('cites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animals');
    }
}
