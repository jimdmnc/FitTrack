<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Admin', // Add this field
            'last_name' => 'Admin', // Add this field
            'gender' => '', // Add this field
            'phone_number' => '12345678901', // Add this field
            'membership_type' => '', // Add this field
            'start_date' => now()->toDateString(), // Add this field
            'rfid_uid' => '', // Add this field

            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
    }
}
