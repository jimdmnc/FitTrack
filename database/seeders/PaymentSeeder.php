<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        Payment::create([
            'member_id' => 1, // John Doe
            'amount' => 100.00,
            'payment_date' => now()->toDateString(),
            'status' => 'paid',
        ]);

        Payment::create([
            'member_id' => 2, // Jane Smith
            'amount' => 50.00,
            'payment_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        // Optionally, use factories to generate more payments
        \App\Models\Payment::factory()->count(20)->create();
    }
}