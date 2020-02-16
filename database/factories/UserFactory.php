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
        'role_id'    => $faker->numberBetween(Role::ADMINISTRATOR, Role::USER)
    ];
});

$factory->state(User::class, 'administrator', ['role_id' => Role::ADMINISTRATOR]);
$factory->state(User::class, 'employee', ['role_id' => Role::EMPLOYEE]);
$factory->state(User::class, 'user', ['role_id' => Role::USER]);
