<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'first_name'   => $faker->firstName,
        'last_name'    => $faker->lastName,
        'email'        => $faker->unique()->safeEmail,
        'title'        => $faker->randomElement(['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Ing.']),
        'mobile_phone' => $faker->phoneNumber,
        //'language' => $faker->locale
    ];
});
