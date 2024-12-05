<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldSpecialtyToContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `contacts` CHANGE `specialty` `specialty` ENUM('AQUARIUM','PARROTS','BIRDS_OF_PREY_OWLS','ONLY_BIRDS','EUROPEAN_SPECIES','GENERAL_ZOO_COLLECTION') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
