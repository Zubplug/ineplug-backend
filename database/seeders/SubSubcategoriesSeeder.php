<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubSubcategory;

class SubSubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        SubSubcategory::create([
            'subcategory_id' => 1, // Modifications
            'name' => 'DOB Change',
            'slug' => 'dob-change',
            'type' => 'manual',
            'fee_type' => 'naira',
            'price' => 200,
            'commission_partner' => 40,
            'commission_agent' => 20,
            'commission_aggregator' => 10,
            'status' => true,
        ]);

        SubSubcategory::create([
            'subcategory_id' => 2, // Non Appearance
            'name' => 'BVN Linking',
            'slug' => 'bvn-linking',
            'type' => 'manual',
            'fee_type' => 'naira',
            'price' => 250,
            'commission_partner' => 30,
            'commission_agent' => 15,
            'commission_aggregator' => 10,
            'status' => true,
        ]);
    }
}
