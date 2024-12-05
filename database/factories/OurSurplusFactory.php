<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OurSurplus;
use Faker\Generator as Faker;

$factory->define(OurSurplus::class, function (Faker $faker) {
    return [
        'animal_id'      => $faker->randomElement([1, 2]),
        'contact_id'     => 1,
        'quantityM'      => $faker->randomElement([1, 20, 3]),
        'quantityF'      => $faker->randomElement([1, 20, 3]),
        'quantityU'      => $faker->randomElement([1, 2, 30]),
        'country_id'     => $faker->randomElement([1, 2, 3]),
        'origin'         => $faker->randomElement(['cb', 'wc', 'cb_wc', 'range', 'unknown']),
        'age_group'      => $faker->randomElement(['less_1_year', 'between_1_2_years', 'between_1_3_years', 'between_2_3_years', 'more_3_years', 'young', 'young_adult', 'adult', 'different_ages']),
        'bornYear'       => $faker->randomElement(['born 2000', '2009', '2008/2009']),
        'size'           => $faker->randomElement(['less_25cm', 'between_26_30cm', 'between_31_35cm', 'between_36_40cm', 'between_41_45cm', 'between_46_50cm', 'between_51_60cm', 'between_61_70cm', 'between_71_80cm', 'between_81_90cm', 'between_91_100cm', 'between_101_115cm', 'between_106_125cm', 'between_126_135cm', 'between_136_150cm', 'between_151_165cm', 'between_166_175cm', 'between_176_190cm', 'between_191_200cm', 'between_201_225cm', 'between_226_250cm', 'between_251_275cm', 'between_276_300cm', 'between_301_325cm', 'between_326_350cm', 'between_351_375cm', 'between_376_400cm', 'between_401_450cm', 'between_451_500cm']),
        'remarks'        => 'test remarks',
        'intern_remarks' => 'test intern remarks',
        'sale_currency'  => $faker->randomElement(['EUR', 'USD', 'GBP', 'CAD']),
        'salePriceM'     => $faker->randomElement([1000, 2000, 3000]),
        'salePriceF'     => $faker->randomElement([1000, 2000, 3000]),
        'salePriceU'     => $faker->randomElement([1000, 2000, 3000]),
        'salePriceP'     => $faker->randomElement([1000, 2000, 3000]),
        'is_public'      => $faker->boolean(90),
    ];
});
