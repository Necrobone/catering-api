<?php

/** @var Factory $factory */

use App\Province;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Province::class, function (Faker $faker) {
    return [
        'id'   => $faker->unique()->randomNumber(2),
        'name' => $faker->name,
    ];
});
