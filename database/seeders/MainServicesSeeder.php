<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainService;

class MainServicesSeeder extends Seeder
{
    public function run(): void
    {
        MainService::create([
            'name' => 'BVN Services',
            'slug' => 'bvn-services',
            'icon' => 'bvn.png',
            'status' => true,
        ]);

        MainService::create([
            'name' => 'NIMC Services',
            'slug' => 'nimc-services',
            'icon' => 'nimc.png',
            'status' => true,
        ]);

        MainService::create([
            'name' => 'CAC Services',
            'slug' => 'cac-services',
            'icon' => 'cac.png',
            'status' => true,
        ]);
    }
}
