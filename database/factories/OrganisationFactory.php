<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Organisation;
use Faker\Generator as Faker;

$factory->define(Organisation::class, function (Faker $faker) {
    return [
        'name'              => $faker->company,
        'organisation_type' => $faker->randomElement(['AFOO', 'HORES', 'CONS']),
        'email'             => $faker->unique()->safeEmail,
        'phone'             => $faker->phoneNumber,
        'website'           => $faker->domainName,
        'address'           => $faker->address,
        'zipcode'           => $faker->postcode,
        'city'              => $faker->city,
        'country_id'        => $faker->randomElement([1, 2, 3]),
        'vat_number'        => null,
        'language'          => $faker->locale,
        'level'             => $faker->randomElement(['A', 'B', 'C']),
        'remarks'           => null,
        'internal_remarks'  => null,
    ];
});
