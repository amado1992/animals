<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crates', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->string('name', 50);
            $table->integer('iata_code')->unsigned()->nullable();
            $table->enum('type', ['', 'Standard', 'Estimation'])->default('');
            $table->integer('animal_quantity')->nullable();
            $table->integer('length')->unsigned()->default(0);
            $table->integer('wide')->unsigned()->default(0);
            $table->integer('height')->unsigned()->default(0);
            $table->double('weight')->nullable();
            $table->string('currency', 5)->nullable();
            $table->decimal('cost_price')->nullable();
            $table->boolean('cost_price_changed')->default(false);
            $table->decimal('sale_price')->nullable();
            $table->boolean('sale_price_changed')->default(false);
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
        Schema::dropIfExists('crates');
    }
}
