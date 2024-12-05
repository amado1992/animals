<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->enum('creator', ['IZS', 'Client'])->default('IZS');
            $table->integer('offer_number')->default(0);
            $table->string('offer_currency', 5)->nullable();
            $table->foreignId('client_id')->constrained('contacts');
            $table->foreignId('supplier_id')->nullable()->constrained('contacts');
            $table->foreignId('delivery_country_id')->nullable()->constrained('countries');
            $table->foreignId('delivery_airport_id')->nullable()->constrained('airports');
            $table->enum('offer_status', ['Inquiry', 'Pending', 'Approval', 'Prospect', 'Cancelled', 'Ordered'])->default('Inquiry');
            $table->text('remarks', 255)->nullable();
            $table->enum('sale_price_type', ['ExZoo', 'FOB', 'CF', 'CIF'])->default('ExZoo');
            $table->enum('cost_price_status', ['Estimation', 'Exactly'])->default('Estimation');
            $table->enum('airfreight_type', ['volKgRates', 'byTruck', 'pallets'])->default('volKgRates');
            $table->boolean('quantity_x')->default(false);
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
        Schema::dropIfExists('offers');
    }
}
