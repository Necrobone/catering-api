<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DishesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dishes')->insert([
            'name' => 'Taco Mexicano',
            'description' => 'Taco con pechuga de pollo y chipotle picante, tostado con guacamole. Irresistible.',
            'image' => 'https://images.pexels.com/photos/461198/pexels-photo-461198.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
            'created_at' => now()
        ]);

        DB::table('dishes')->insert([
            'name' => 'Arroz con Zucchini, huevo cocido y perejil',
            'description' => 'Plato ligero, sano y sabroso. No lo dudes.',
            'image' => 'https://images.pexels.com/photos/1410235/pexels-photo-1410235.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
            'created_at' => now()
        ]);

        DB::table('dishes')->insert([
            'name' => 'Pancake con fresa y helado',
            'description' => 'Delicioso pancake con helado y fresas, querrás repetir.',
            'image' => 'https://images.pexels.com/photos/376464/pexels-photo-376464.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
            'created_at' => now()
        ]);
    }
}
