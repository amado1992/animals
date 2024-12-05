<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGuidelineSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guidelines', function (Blueprint $table) {
            DB::statement("ALTER TABLE `guidelines` CHANGE `section` `section` ENUM('guidelines','protocols_general','protocols_data_picture_entry','protocols_offers','protocols_transport','protocols_others','offers_reservations_contracts') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'guidelines';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guidelines', function (Blueprint $table) {
            //
        });
    }
}
