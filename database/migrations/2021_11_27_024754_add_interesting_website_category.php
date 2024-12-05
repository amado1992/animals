<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInterestingWebsiteCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interesting_websites', function (Blueprint $table) {
            DB::statement("ALTER TABLE `interesting_websites` CHANGE `siteCategory` `siteCategory` ENUM('website-templates','animals-pictures','task-managers','outsource-tasks','tools-informatic','website-development','regulations','others','other-credentials','our-links') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interesting_websites', function (Blueprint $table) {
            //
        });
    }
}
