<?php

use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'Istrador',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('administrator'),
            'api_token' => Str::random(50),
            'role_id' => Role::ADMINISTRATOR,
            'created_at' => now()
        ]);

        DB::table('users')->insert([
            'first_name' => 'Emple',
            'last_name' => 'Ado',
            'email' => 'empleado@gmail.com',
            'password' => Hash::make('employee'),
            'api_token' => Str::random(50),
            'role_id' => Role::EMPLOYEE,
            'created_at' => now()
        ]);

        DB::table('users')->insert([
            'first_name' => 'Cli',
            'last_name' => 'Ente',
            'email' => 'cliente@gmail.com',
            'password' => Hash::make('user'),
            'api_token' => Str::random(50),
            'role_id' => Role::USER,
            'created_at' => now()
        ]);
    }
}
