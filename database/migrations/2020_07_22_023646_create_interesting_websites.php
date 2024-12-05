<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestingWebsites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interesting_websites', function (Blueprint $table) {
            $table->id();
            $table->string('siteName', 255)->nullable();
            $table->string('siteUrl', 255)->nullable();
            $table->string('siteRemarks', 255)->nullable();
            $table->string('loginUsername', 255)->nullable();
            $table->string('loginPassword', 1000)->nullable();
            $table->enum('siteCategory', ['website-templates', 'animals-pictures', 'task-managers', 'outsource-tasks', 'tools-informatic', 'website-development', 'regulations', 'others', 'other-credentials'])->nullable();
            $table->boolean('only_for_john')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interesting_websites');
    }
}
