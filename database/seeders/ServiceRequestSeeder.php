<?php

// database/seeders/ServiceRequestSeeder.php

namespace Database\Seeders;

use App\Models\ServiceRequest;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequest::create([
            'user_name' => 'Inemesit Akpan',
            'service_type' => 'BVN',
            'main_service_id' => 1,
            'subcategory_id' => 1,
            'sub_subcategory_id' => 1,
            'status' => 'pending',
            'submitted_data' => json_encode([
                'dob' => '1995-01-01',
                'nin' => '12345678901',
                'reason' => 'DOB mismatch on bank account',
            ])
        ]);
    }
}
