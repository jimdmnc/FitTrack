<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'gender' => 'male',
            'phone_number' => '09171234567',
            'membership_type' => 'staff',
            'start_date' => now()->toDateString(),
            'rfid_uid' => 'STAFF1234',
            'email' => 'testadmin@gmail.com',
            'birthdate' => '1990-01-01',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),

            'role' => 'super_admin',
            'member_status' => 'active',
            'revoke_reason' => null,
            'revoked_at' => null,
            'session_status' => 'pending',
            'rejection_reason' => null,
            'needs_approval' => 0,
        ]);
    }
}
