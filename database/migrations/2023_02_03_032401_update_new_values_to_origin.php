<?php

use App\Enums\Origin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewValuesToOrigin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('origins', function (Blueprint $table) {
            $origin = Origin::get();
            $date   = new DateTime('now');
            $date   = $date->format('Y-m-d H:i:s');
            DB::statement("INSERT INTO origins (name, short_cut, created_at, updated_at) VALUES ('Empty', 'empty', '" . $date . "', '" . $date . "')");
            foreach ($origin as $key => $value) {
                DB::statement("INSERT INTO origins (name, short_cut, created_at, updated_at) VALUES ('" . $value . "', '" . $key . "', '" . $date . "', '" . $date . "')");
            }
            DB::statement("INSERT INTO origins (name, short_cut, created_at, updated_at) VALUES ('Stuffed', 'stuffed', '" . $date . "', '" . $date . "')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
