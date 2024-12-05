<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_action', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('action_id')->constrained()->onDelete('cascade');
            $table->timestamp('action_date')->nullable();
            $table->timestamp('action_remind_date')->nullable();
            $table->timestamp('action_received_date')->nullable();
            $table->string('action_document', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->enum('toBeDoneBy', ['IZS', 'Client', 'Supplier', 'Transport'])->nullable();
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
        Schema::dropIfExists('order_action');
    }
}
