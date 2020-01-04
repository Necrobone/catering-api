<?php

use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => Role::ADMINISTRATOR,
            'name' => 'Administrator',
        ]);
        DB::table('roles')->insert([
            'id' => Role::EMPLOYEE,
            'name' => 'Employee',
        ]);
        DB::table('roles')->insert([
            'id' => Role::USER,
            'name' => 'User',
        ]);
    }
}
