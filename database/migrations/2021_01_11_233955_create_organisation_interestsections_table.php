<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationInterestsectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_interestsections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade');
            $table->string('interest_section_key', 5);

            $table->foreign('interest_section_key')
                ->references('key')
                ->on('interest_sections')
                ->constrained('interest_sections')
                ->onDelete('cascade');

            /*$table->foreign('interest_section_key')->references('key')->on('interest_sections')
                    ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisation_interestsections');
    }
}
