<?php

namespace Database\Factories;

use App\Models\Surgery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Surgery>
 */
class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition(): array
    {
        return [
            'doctor_id' => User::factory(),
            'patient_name' => $this->faker->name(),
            'starts_at' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'ends_at' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => Surgery::STATUS_SCHEDULED,
        ];
    }
}
