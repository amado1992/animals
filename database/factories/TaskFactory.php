<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        //'label' => $faker->sentence(3),
        'description' => $faker->sentence(12),
        'action'      => $faker->randomElement(['Call again', 'Sent reminder', 'Ask for documents']),
        'due_date'    => $faker->optional()->dateTimeBetween('2 weeks', '4 weeks'),
        'user_id'     => $faker->optional()->randomElement([1]),
        'created_by'  => $faker->optional()->randomElement([1]),
        //'finished_at' => $faker->optional()->dateTimeBetween('now', '4 weeks'),
    ];
});
