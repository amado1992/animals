<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->double('usdCostPrice')->default(0);
            $table->double('usdSalePrice')->default(0);
            $table->double('eurCostPrice')->default(0);
            $table->double('eurSalePrice')->default(0);
            $table->boolean('is_test')->default(false);
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
        Schema::dropIfExists('additional_costs');
    }
}
