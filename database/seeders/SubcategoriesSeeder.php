<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;

class SubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        Subcategory::create([
            'main_service_id' => 1, // BVN Services
            'name' => 'Modifications',
            'slug' => 'modifications',
            'type' => 'manual',
            'fee_type' => 'naira',
            'price' => 300,
            'commission_partner' => 50,
            'commission_agent' => 30,
            'commission_aggregator' => 20,
            'status' => true,
        ]);

        Subcategory::create([
            'main_service_id' => 2, // NIMC Services
            'name' => 'Non Appearance',
            'slug' => 'non-appearance',
            'type' => 'manual',
            'fee_type' => 'naira',
            'price' => 400,
            'commission_partner' => 60,
            'commission_agent' => 40,
            'commission_aggregator' => 25,
            'status' => true,
        ]);
    }
}
