<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_email', 100);
            $table->string('name');
            $table->string('guid', 250);
            $table->text('subject');
            $table->longText('body');
            $table->boolean('is_read')->default(1);
            $table->foreignId('directorie_id')->nullable()->constrained('directories');
            $table->foreignId('contact_id')->nullable()->constrained('contacts');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('offer_id')->nullable()->constrained('offers');
            $table->foreignId('wanted_id')->nullable()->constrained('wanted');
            $table->boolean('is_send')->default(0);
            $table->boolean('is_delete')->default(0);
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
        Schema::dropIfExists('inbox_email');
    }
}
