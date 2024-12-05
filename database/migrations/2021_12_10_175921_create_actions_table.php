<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['reservation', 'permit', 'veterinary', 'crate', 'transport', 'offer']);
            $table->string('action_description', 255);
            $table->string('action_code', 255);
            $table->enum('toBeDoneBy', ['IZS', 'Client', 'Supplier', 'Transport'])->default('IZS');
            $table->enum('belongs_to', ['Offer', 'Order', 'Offer_Order'])->default('Order');
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
        Schema::dropIfExists('actions');
    }
}
