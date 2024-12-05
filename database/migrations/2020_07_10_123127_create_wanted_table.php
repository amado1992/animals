<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWantedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wanted', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('contacts');
            $table->foreignId('animal_id')->constrained();
            $table->enum('origin', ['cb', 'wc', 'cb_wc', 'range', 'unknown'])->nullable();
            $table->enum('age_group', ['less_1_year', 'between_1_2_years', 'between_1_3_years', 'between_2_3_years', 'more_3_years', 'young', 'young_adult', 'adult', 'different_ages'])->nullable();
            $table->enum('looking_for', ['groups', 'pairs', 'pairs_single_males', 'pairs_single_females', 'single_females', 'single_males', 'any', 'see_remarks'])->nullable();
            $table->string('remarks', 255)->nullable();
            $table->text('intern_remarks')->nullable();
            $table->foreignId('inserted_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('wanted');
    }
}
