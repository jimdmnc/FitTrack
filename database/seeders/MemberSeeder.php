<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        // Skip seeding existing members
        // Member::create([
        //     'name' => 'John Doe',
        //     'email' => 'john.doe@example.com',
        //     'membership_type' => 'Premium',
        //     'join_date' => now()->toDateString(),
        //     'rfid_uid' => '1234567890',
        // ]);

        // Member::create([
        //     'name' => 'Jane Smith',
        //     'email' => 'jane.smith@example.com',
        //     'membership_type' => 'Standard',
        //     'join_date' => now()->toDateString(),
        //     'rfid_uid' => '0987654321',
        // ]);

        // Use factories to generate more members
        \App\Models\Member::factory()->count(10)->create();
    }
}