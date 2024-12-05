<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->string('common_name', 255);
            $table->string('common_name_slug', 191)->nullable()->unique();
            $table->string('common_name_spanish', 255)->nullable();
            $table->string('scientific_name', 255);

            $table->string('code')->nullable();
            $table->foreignId('belongs_to')->nullable()->constrained('classifications');
            $table->enum('rank', ['genus', 'family', 'order', 'class']);
            $table->timestamps();

            /*$table->unique(['code','rank']);*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classifications');
    }
}
