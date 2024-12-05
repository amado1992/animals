<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStdTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('std_texts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->enum('category', ['contacts', 'animals-stock', 'order-docs', 'airfreight', 'crates', 'permits-certificates', 'booking', 'arrival-details', 'payment', 'general', 'website'])->nullable()->default('general');
            $table->string('name', 255)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->text('english_text')->nullable();
            $table->text('spanish_text')->nullable();
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
        Schema::dropIfExists('std_texts');
    }
}
