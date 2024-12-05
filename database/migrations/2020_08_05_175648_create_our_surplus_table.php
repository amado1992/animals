<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOurSurplusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('our_surplus', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->foreignId('animal_id')->constrained();
            $table->enum('availability', ['usually', 'regularly', 'occasionally', 'rarely', 'extremely_rare'])->nullable();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->foreignId('area_region_id')->nullable()->constrained('area_regions');
            $table->enum('origin', ['cb', 'wc', 'cb_wc', 'range', 'unknown'])->nullable();
            $table->enum('age_group', ['less_1_year', 'between_1_2_years', 'between_1_3_years', 'between_2_3_years', 'more_3_years', 'young', 'young_adult', 'adult', 'different_ages'])->nullable();
            $table->string('bornYear', 255)->nullable();
            $table->enum('size', ['less_25cm', 'between_26_30cm', 'between_31_35cm', 'between_36_40cm', 'between_41_45cm', 'between_46_50cm', 'between_51_60cm', 'between_61_70cm', 'between_71_80cm', 'between_81_90cm', 'between_91_100cm', 'between_101_115cm', 'between_106_125cm', 'between_126_135cm', 'between_136_150cm', 'between_151_165cm', 'between_166_175cm', 'between_176_190cm', 'between_191_200cm', 'between_201_225cm', 'between_226_250cm', 'between_251_275cm', 'between_276_300cm', 'between_301_325cm', 'between_326_350cm', 'between_351_375cm', 'between_376_400cm', 'between_401_450cm', 'between_451_500cm'])->nullable();
            $table->string('remarks', 255)->nullable();
            $table->string('intern_remarks', 255)->nullable();
            $table->string('special_conditions', 255)->nullable();
            $table->string('cost_currency', 5)->nullable();
            $table->double('costPriceM')->default(0);
            $table->double('costPriceF')->default(0);
            $table->double('costPriceU')->default(0);
            $table->double('costPriceP')->default(0);
            $table->string('sale_currency', 5)->nullable();
            $table->double('salePriceM')->default(0);
            $table->double('salePriceF')->default(0);
            $table->double('salePriceU')->default(0);
            $table->double('salePriceP')->default(0);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('our_surplus');
    }
}
