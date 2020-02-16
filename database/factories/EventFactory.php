<?php

/** @var Factory $factory */

use App\Event;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
