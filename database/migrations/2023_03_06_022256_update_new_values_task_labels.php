<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewValuesTaskLabels extends Migration
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
        DB::statement("INSERT INTO labels (title, name, color, created_at, updated_at) VALUES ('Task', 'task', '#22d340', '" . $date . "', '" . $date . "')");
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
