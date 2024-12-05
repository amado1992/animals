<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZooAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoo_associations', function (Blueprint $table) {
            $table->id();
            $table->string('area', 100)->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('website', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->enum('status', ['Interesting', 'Very interesting', 'Not interesting', 'Done', 'Error'])->nullable();
            $table->timestamp('started_date')->nullable();
            $table->timestamp('checked_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
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
        Schema::dropIfExists('zoo_associations');
    }
}
