<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            'address' => 'C/ Gaztambide 40',
            'zip' => '28017',
            'city' => 'Madrid',
            'start_date' => now()->addDays(7),
            'province_id' => 28,
            'event_id' => 1,
            'created_at' => now()
        ]);

        DB::table('service_dishes')->insert([
            'service_id' => 1,
            'dish_id' => 1,
        ]);

        DB::table('service_dishes')->insert([
            'service_id' => 1,
            'dish_id' => 2,
        ]);

        DB::table('service_dishes')->insert([
            'service_id' => 1,
            'dish_id' => 3,
        ]);

        DB::table('user_services')->insert([
            'service_id' => 1,
            'user_id' => 2,
        ]);

        DB::table('user_services')->insert([
            'service_id' => 1,
            'user_id' => 3,
        ]);
    }
}
