<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Wanted;
use Faker\Generator as Faker;

$factory->define(Wanted::class, function (Faker $faker) {
    return [
        'client_id'      => $faker->randomElement([1, 2, 3]),
        'animal_id'      => $faker->randomElement([1, 2]),
        'origin'         => $faker->randomElement(['cb', 'wc', 'cb_wc', 'range', 'unknown']),
        'age_group'      => $faker->randomElement(['less_1_year', 'between_1_2_years', 'between_1_3_years', 'between_2_3_years', 'more_3_years', 'young', 'young_adult', 'adult', 'different_ages']),
        'looking_for'    => $faker->randomElement(['groups', 'pairs', 'pairs_single_females', 'single_females', 'single_males', 'any', 'see_remarks']),
        'remarks'        => 'test remarks',
        'intern_remarks' => 'test intern remarks',
        'inserted_by'    => 1,
    ];
});
