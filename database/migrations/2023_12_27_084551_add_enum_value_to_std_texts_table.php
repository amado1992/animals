<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnumValueToStdTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      \DB::statement("ALTER TABLE std_texts "
            . "MODIFY COLUMN category "
            . "ENUM('airfreight','animals-stock','arrival-details','booking','contacts','crates','email','general','order-docs','payment','permits-certificates','website');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      \DB::statement("ALTER TABLE std_texts "
               . "MODIFY COLUMN category "
               . "ENUM('airfreight','animals-stock','arrival-details','booking','contacts','crates','general','order-docs','payment','permits-certificates','website');");
    }
}
