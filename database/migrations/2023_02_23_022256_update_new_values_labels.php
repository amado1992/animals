<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewValuesLabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $date = new DateTime('now');
        $date = $date->format('Y-m-d H:i:s');
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('Offer', 'offer', '#37cde6', '" . $date . "', '" . $date . "')");
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('Order', 'order', '#f7b84b', '" . $date . "', '" . $date . "')");
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('Wanted', 'wanted', '#323a46', '" . $date . "', '" . $date . "')");
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('Surplus', 'surplus', '#3bafda', '" . $date . "', '" . $date . "')");
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('New Contact', 'new_contact', '#f1556c', '" . $date . "', '" . $date . "')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
