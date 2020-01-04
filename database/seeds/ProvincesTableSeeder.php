<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provinces')->insert([
            'id' => 1,
            'name' => 'Álava',
        ]);
        DB::table('provinces')->insert([
            'id' => 2,
            'name' => 'Albacete',
        ]);
        DB::table('provinces')->insert([
            'id' => 3,
            'name' => 'Alicante',
        ]);
        DB::table('provinces')->insert([
            'id' => 4,
            'name' => 'Almería',
        ]);
        DB::table('provinces')->insert([
            'id' => 5,
            'name' => 'Ávila',
        ]);
        DB::table('provinces')->insert([
            'id' => 6,
            'name' => 'Badajoz',
        ]);
        DB::table('provinces')->insert([
            'id' => 7,
            'name' => 'Baleares (Illes)',
        ]);
        DB::table('provinces')->insert([
            'id' => 8,
            'name' => 'Barcelona',
        ]);
        DB::table('provinces')->insert([
            'id' => 9,
            'name' => 'Burgos',
        ]);
        DB::table('provinces')->insert([
            'id' => 10,
            'name' => 'Cáceres',
        ]);
        DB::table('provinces')->insert([
            'id' => 11,
            'name' => 'Cádiz',
        ]);
        DB::table('provinces')->insert([
            'id' => 12,
            'name' => 'Castellón',
        ]);
        DB::table('provinces')->insert([
            'id' => 13,
            'name' => 'Ciudad Real',
        ]);
        DB::table('provinces')->insert([
            'id' => 14,
            'name' => 'Córdoba',
        ]);
        DB::table('provinces')->insert([
            'id' => 15,
            'name' => 'A Coruña',
        ]);
        DB::table('provinces')->insert([
            'id' => 16,
            'name' => 'Cuenca',
        ]);
        DB::table('provinces')->insert([
            'id' => 17,
            'name' => 'Girona',
        ]);
        DB::table('provinces')->insert([
            'id' => 18,
            'name' => 'Granada',
        ]);
        DB::table('provinces')->insert([
            'id' => 19,
            'name' => 'Guadalajara',
        ]);
        DB::table('provinces')->insert([
            'id' => 20,
            'name' => 'Guipúzcoa',
        ]);
        DB::table('provinces')->insert([
            'id' => 21,
            'name' => 'Huelva',
        ]);
        DB::table('provinces')->insert([
            'id' => 22,
            'name' => 'Huesca',
        ]);
        DB::table('provinces')->insert([
            'id' => 23,
            'name' => 'Jaén',
        ]);
        DB::table('provinces')->insert([
            'id' => 24,
            'name' => 'León',
        ]);
        DB::table('provinces')->insert([
            'id' => 25,
            'name' => 'Lleida',
        ]);
        DB::table('provinces')->insert([
            'id' => 26,
            'name' => 'La Rioja',
        ]);
        DB::table('provinces')->insert([
            'id' => 27,
            'name' => 'Lugo',
        ]);
        DB::table('provinces')->insert([
            'id' => 28,
            'name' => 'Madrid',
        ]);
        DB::table('provinces')->insert([
            'id' => 29,
            'name' => 'Málaga',
        ]);
        DB::table('provinces')->insert([
            'id' => 30,
            'name' => 'Murcia',
        ]);
        DB::table('provinces')->insert([
            'id' => 31,
            'name' => 'Navarra',
        ]);
        DB::table('provinces')->insert([
            'id' => 32,
            'name' => 'Ourense',
        ]);
        DB::table('provinces')->insert([
            'id' => 33,
            'name' => 'Asturias',
        ]);
        DB::table('provinces')->insert([
            'id' => 34,
            'name' => 'Palencia',
        ]);
        DB::table('provinces')->insert([
            'id' => 35,
            'name' => 'Las Palmas',
        ]);
        DB::table('provinces')->insert([
            'id' => 36,
            'name' => 'Pontevedra',
        ]);
        DB::table('provinces')->insert([
            'id' => 37,
            'name' => 'Salamanca',
        ]);
        DB::table('provinces')->insert([
            'id' => 38,
            'name' => 'Santa Cruz de Tenerife',
        ]);
        DB::table('provinces')->insert([
            'id' => 39,
            'name' => 'Cantabria',
        ]);
        DB::table('provinces')->insert([
            'id' => 40,
            'name' => 'Segovia',
        ]);
        DB::table('provinces')->insert([
            'id' => 41,
            'name' => 'Sevilla',
        ]);
        DB::table('provinces')->insert([
            'id' => 42,
            'name' => 'Soria',
        ]);
        DB::table('provinces')->insert([
            'id' => 43,
            'name' => 'Tarragona',
        ]);
        DB::table('provinces')->insert([
            'id' => 44,
            'name' => 'Teruel',
        ]);
        DB::table('provinces')->insert([
            'id' => 45,
            'name' => 'Toledo',
        ]);
        DB::table('provinces')->insert([
            'id' => 46,
            'name' => 'Valencia',
        ]);
        DB::table('provinces')->insert([
            'id' => 47,
            'name' => 'Valladolid',
        ]);
        DB::table('provinces')->insert([
            'id' => 48,
            'name' => 'Vizcaya',
        ]);
        DB::table('provinces')->insert([
            'id' => 49,
            'name' => 'Zamora',
        ]);
        DB::table('provinces')->insert([
            'id' => 50,
            'name' => 'Zaragoza',
        ]);
        DB::table('provinces')->insert([
            'id' => 51,
            'name' => 'Ceuta',
        ]);
        DB::table('provinces')->insert([
            'id' => 52,
            'name' => 'Melilla',
        ]);
    }
}
