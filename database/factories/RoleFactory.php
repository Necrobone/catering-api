<?php

/** @var Factory $factory */

use App\Role;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'id'   => $faker->numberBetween(Role::ADMINISTRATOR, Role::USER),
        'name' => $faker->name,
    ];
});

$factory->state(Role::class, 'administrator', [
    'id' => Role::ADMINISTRATOR
]);

$factory->state(Role::class, 'employee', [
    'id' => Role::EMPLOYEE
]);

$factory->state(Role::class, 'user', [
    'id' => Role::USER
]);
