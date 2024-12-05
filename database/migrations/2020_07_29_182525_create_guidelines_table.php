<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->enum('category', ['general', 'requests', 'orders', 'contacts', 'mailing'])->nullable()->default('general');
            $table->enum('section', ['guidelines', 'protocols_general', 'protocols_data_picture_entry', 'protocols_offers', 'protocols_transport', 'protocols_others'])->default('guidelines');
            $table->string('related_filename', 255)->nullable();
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
        Schema::dropIfExists('guidelines');
    }
}
