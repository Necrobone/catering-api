<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeadquartersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('headquarters')->insert([
            'name' => 'Sede principal',
            'address' => 'C/ Acentejo 4, 1º Izquierda',
            'zip' => '28017',
            'city' => 'Madrid',
            'province_id' => 28,
            'created_at' => now()
        ]);

        DB::table('headquarters')->insert([
            'name' => 'Sede secundaria',
            'address' => 'C/ Alhambra, 11',
            'zip' => '04720',
            'city' => 'El Campillo del Moro',
            'province_id' => 4,
            'created_at' => now()
        ]);
    }
}
