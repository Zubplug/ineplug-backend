<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MainServicesSeeder::class,
            SubcategoriesSeeder::class,
            SubSubcategoriesSeeder::class,
            ServiceRequestSeeder::class,
        ]);
    }
}
