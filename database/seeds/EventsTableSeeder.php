<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            'name' => 'Boda',
            'created_at' => now()
        ]);

        DB::table('events')->insert([
            'name' => 'Bautizo',
            'created_at' => now()
        ]);

        DB::table('events')->insert([
            'name' => 'Comunión',
            'created_at' => now()
        ]);

        DB::table('event_dishes')->insert([
            'event_id' => 1,
            'dish_id' => 1,
        ]);

        DB::table('event_dishes')->insert([
            'event_id' => 2,
            'dish_id' => 2,
        ]);

        DB::table('event_dishes')->insert([
            'event_id' => 3,
            'dish_id' => 3,
        ]);

        DB::table('event_menus')->insert([
            'event_id' => 1,
            'menu_id' => 1,
        ]);
    }
}
