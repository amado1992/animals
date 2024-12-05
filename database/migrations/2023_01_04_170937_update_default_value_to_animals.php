<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultValueToAnimals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE animals ALTER COLUMN ztl_class SET DEFAULT '';");
        \DB::statement("ALTER TABLE animals ALTER COLUMN ztl_order SET DEFAULT '';");
        \DB::statement("ALTER TABLE animals ALTER COLUMN ztl_family SET DEFAULT '';");
        \DB::statement("ALTER TABLE animals ALTER COLUMN ztl_article SET DEFAULT '';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE animals ALTER COLUMN ztl_class NOT NULL;');
        \DB::statement('ALTER TABLE animals ALTER COLUMN ztl_order NOT NULL;');
        \DB::statement('ALTER TABLE animals ALTER COLUMN ztl_family NOT NULL;');
        \DB::statement('ALTER TABLE animals ALTER COLUMN ztl_article NOT NULL;');
    }
}
