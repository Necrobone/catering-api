<?php

/** @var Factory $factory */

use App\Dish;
use App\Event;
use App\Supplier;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Dish::class, function (Faker $faker) {
    return [
        'name'        => $faker->name,
        'description' => $faker->text(),
        'image'       => $faker->imageUrl(),
    ];
});

$factory->afterCreating(Dish::class, function ($dish, $faker) {
    $dish->events()->saveMany(factory(Event::class, 2)->create());
    $dish->suppliers()->saveMany(factory(Supplier::class, 2)->create());
});
