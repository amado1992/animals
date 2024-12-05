<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOfferActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('offer_action', function (Blueprint $table) {
            $table->enum('status', ['pending', 'done'])->after('toBeDoneBy')->default('pending');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('offer_action', function (Blueprint $table) {
            $table->dropColumn('status');
         });
    }
}
