<?php


namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'membership_type' => $this->faker->randomElement(['Standard', 'Premium']),
            'join_date' => $this->faker->date,
            'rfid_uid' => $this->faker->unique()->numerify('##########'),
        ];
    }
}