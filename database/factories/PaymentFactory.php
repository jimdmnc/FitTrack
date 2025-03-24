<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'member_id' => \App\Models\Member::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'payment_date' => $this->faker->date,
            'status' => $this->faker->randomElement(['paid', 'pending']),
        ];
    }
}