<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchMailingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_mailings', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('searchable');
            $table->foreignId('animal_id')->constrained();
            $table->timestamp('date_sent_out')->nullable();
            $table->timestamp('next_reminder_at')->nullable();
            $table->integer('times_reminded')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('search_mailings');
    }
}
