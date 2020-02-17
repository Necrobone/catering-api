<?php

/** @var Factory $factory */

use App\Dish;
use App\Event;
use App\Menu;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Menu::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->afterCreating(Menu::class, function ($menu, $faker) {
    $menu->dishes()->saveMany(factory(Dish::class, 2)->create());
    $menu->events()->saveMany(factory(Event::class, 2)->create());
});
