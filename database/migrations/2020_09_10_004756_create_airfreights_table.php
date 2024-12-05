<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirfreightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfreights', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['', 'estimation', 'offer'])->default('');
            $table->enum('type', ['volKg', 'lowerdeck', 'maindeck'])->nullable();
            $table->foreignId('departure_continent')->constrained('regions');
            $table->foreignId('arrival_continent')->constrained('regions');
            $table->string('currency', 5)->nullable();
            $table->double('volKg_weight_value')->default(0);
            $table->double('volKg_weight_cost')->default(0);
            $table->double('lowerdeck_value')->default(0);
            $table->double('lowerdeck_cost')->default(0);
            $table->double('maindeck_value')->default(0);
            $table->double('maindeck_cost')->default(0);
            $table->timestamp('offered_date')->nullable();
            $table->foreignId('transport_agent')->nullable()->constrained('contacts');
            $table->string('remarks', 255)->nullable();
            $table->foreignId('inserted_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('airfreights');
    }
}
