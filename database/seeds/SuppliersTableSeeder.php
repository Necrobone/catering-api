<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            'name' => 'Proveedor principal',
            'created_at' => now()
        ]);

        DB::table('suppliers')->insert([
            'name' => 'Proveedor secundario',
            'created_at' => now()
        ]);

        DB::table('suppliers')->insert([
            'name' => 'Proveedor nacional',
            'created_at' => now()
        ]);

        DB::table('supplier_dishes')->insert([
            'supplier_id' => 1,
            'dish_id' => 1,
        ]);

        DB::table('supplier_dishes')->insert([
            'supplier_id' => 2,
            'dish_id' => 2,
        ]);

        DB::table('supplier_dishes')->insert([
            'supplier_id' => 3,
            'dish_id' => 3,
        ]);

        DB::table('supplier_headquarters')->insert([
            'supplier_id' => 1,
            'headquarter_id' => 1,
        ]);

        DB::table('supplier_headquarters')->insert([
            'supplier_id' => 2,
            'headquarter_id' => 2,
        ]);

        DB::table('supplier_headquarters')->insert([
            'supplier_id' => 3,
            'headquarter_id' => 1,
        ]);

        DB::table('supplier_headquarters')->insert([
            'supplier_id' => 3,
            'headquarter_id' => 2,
        ]);
    }
}
