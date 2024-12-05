<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('offer_number');
            $table->foreignId('contact_id')->constrained();
            $table->enum('source', ['open', 'pending', 'hold', 'search', 'offer']);
            $table->enum('status', ['web', 'mail', 'crm']);
            $table->enum('terms', ['CF', 'EX'])->nullable();
            $table->string('summary')->nullable();
            $table->foreignId('delivery_country')->nullable()->constrained('countries');
            $table->foreignId('delivery_airport')->nullable()->constrained('airports');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->longText('client_remarks')->nullable();
            $table->longText('intern_remarks')->nullable();
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
        Schema::dropIfExists('price_requests');
    }
}
