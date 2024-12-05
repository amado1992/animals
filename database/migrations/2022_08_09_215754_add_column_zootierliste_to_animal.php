<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnZootierlisteToAnimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animals', function (Blueprint $table) {
            //
            $table->string('ztl_class', 100);
            $table->string('ztl_order', 100);
            $table->string('ztl_family', 100);
            $table->string('ztl_article', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('animal', function (Blueprint $table) {
            //
            $table->dropColumn('ztl_class');
            $table->dropColumn('ztl_order');
            $table->dropColumn('ztl_family');
            $table->dropColumn('ztl_article');
        });
    }
}
