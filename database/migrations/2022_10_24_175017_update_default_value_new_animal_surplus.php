<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultValueNewAnimalSurplus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("UPDATE surplus SET new_animal = 0 WHERE created_at < '2022-10-21' OR created_at IS NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("UPDATE surplus SET new_animal = 0 WHERE created_at < '2022-10-21' OR created_at IS NULL");
    }
}
