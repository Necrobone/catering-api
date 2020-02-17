<?php

/** @var Factory $factory */

use App\Dish;
use App\Event;
use App\Province;
use App\Service;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Service::class, function (Faker $faker) {
    return [
        'address'     => $faker->streetAddress,
        'zip'         => $faker->postcode,
        'city'        => $faker->city,
        'start_date'  => $faker->dateTimeBetween('+1 day', '+1 year'),
        'approved'    => null,
        'province_id' => Province::first(),
        'event_id'    => factory(Event::class),
    ];
});

$factory->afterCreating(Service::class, function ($service, $faker) {
    $service->dishes()->saveMany(factory(Dish::class, 2)->create());
    $service->users()->saveMany(factory(User::class, 2)->create());
});
