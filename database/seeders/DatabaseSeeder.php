<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com', // Change to your actual admin email
            'password' => Hash::make('admin123'), // ✅ Hash the password correctly
            'role' => 'admin', // Ensure your 'users' table has a 'role' column
        ]);
    }
}
