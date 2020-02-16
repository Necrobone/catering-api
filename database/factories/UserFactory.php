<?php

use App\Role;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/** @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name'  => $faker->lastName,
        'email'      => $faker->unique()->safeEmail,
        'password'   => $faker->password,
        'api_token'  => $faker->md5,
        'role_id'    => factory(Role::class)
    ];
});

$factory->state(User::class, 'administrator', [
    'role_id' => function () {
        return factory(Role::class)->state('administrator')->create()->id;
    }
]);

$factory->state(User::class, 'employee', [
    'role_id' => function () {
        return factory(Role::class)->state('employee')->create()->id;
    }
]);

$factory->state(User::class, 'user', [
    'role_id' => function () {
        return factory(Role::class)->state('user')->create()->id;
    }
]);
