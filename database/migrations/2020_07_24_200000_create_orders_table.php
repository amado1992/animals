<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->integer('order_number')->default(0);
            $table->foreignId('client_id')->constrained('contacts');
            $table->foreignId('supplier_id')->nullable()->constrained('contacts');
            $table->foreignId('contact_origin_id')->nullable()->constrained('contacts');
            $table->foreignId('contact_final_destination_id')->nullable()->constrained('contacts');
            $table->foreignId('delivery_country_id')->constrained('countries');
            $table->foreignId('delivery_airport_id')->constrained('airports');
            $table->string('cost_currency', 5)->nullable();
            $table->string('sale_currency', 5)->nullable();
            $table->enum('company', ['IZS_Inc', 'IZS_BV', 'Personal'])->nullable();
            $table->foreignId('bank_account_id')->nullable()->constrained();
            $table->enum('order_status', ['Pending', 'ToSearch', 'Realized', 'Cancelled'])->default('Pending');
            $table->string('order_remarks', 255)->nullable();
            $table->enum('cost_price_type', ['ExZoo', 'FOB', 'CF', 'CIF'])->default('ExZoo');
            $table->enum('sale_price_type', ['ExZoo', 'FOB', 'CF', 'CIF'])->default('ExZoo');
            $table->enum('cost_price_status', ['Estimation', 'Exactly'])->default('Estimation');
            $table->double('total_profit')->default(0);
            $table->timestamp('realized_date')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
