<?php

/** @var Factory $factory */

use App\Headquarter;
use App\Supplier;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Supplier::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->afterCreating(Supplier::class, function ($supplier, $faker) {
    $supplier->headquarters()->saveMany(factory(Headquarter::class, 2)->create());
});
