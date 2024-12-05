<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldCatalogPicToOurSurplus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('our_surplus', function (Blueprint $table) {
            $table->string('catalog_pic', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('our_surplus', function (Blueprint $table) {
            $table->dropColumn('catalog_pic');
        });
    }
}
