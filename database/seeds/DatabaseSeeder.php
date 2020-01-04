<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProvincesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(HeadquartersTableSeeder::class);
        $this->call(DishesTableSeeder::class);
        $this->call(SuppliersTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
    }
}
