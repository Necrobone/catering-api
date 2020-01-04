<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            'name' => 'Menú de ejemplo',
            'created_at' => now()
        ]);

        DB::table('menu_dishes')->insert([
            'menu_id' => 1,
            'dish_id' => 1,
        ]);

        DB::table('menu_dishes')->insert([
            'menu_id' => 1,
            'dish_id' => 2,
        ]);

        DB::table('menu_dishes')->insert([
            'menu_id' => 1,
            'dish_id' => 3,
        ]);
    }
}
