<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->unique();
            $table->decimal('EUR_USD', 10, 5);
            $table->decimal('EUR_GBP', 10, 5);
            $table->decimal('EUR_CAD', 10, 5);
            $table->decimal('USD_EUR', 10, 5);
            $table->decimal('USD_GBP', 10, 5);
            $table->decimal('USD_CAD', 10, 5);
            $table->decimal('GBP_EUR', 10, 5);
            $table->decimal('GBP_USD', 10, 5);
            $table->decimal('GBP_CAD', 10, 5);
            $table->decimal('CAD_EUR', 10, 5);
            $table->decimal('CAD_USD', 10, 5);
            $table->decimal('CAD_GBP', 10, 5);
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
        Schema::dropIfExists('currency_rates');
    }
}
