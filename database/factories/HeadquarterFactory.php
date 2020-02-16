<?php

/** @var Factory $factory */

use App\Headquarter;
use App\Province;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Headquarter::class, function (Faker $faker) {
    return [
        'name'        => $faker->streetName,
        'address'     => $faker->streetAddress,
        'zip'         => $faker->postcode,
        'city'        => $faker->city,
        'province_id' => Province::first(),
    ];
});
