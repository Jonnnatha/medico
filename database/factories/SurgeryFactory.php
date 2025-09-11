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
            'duration_min' => $this->faker->numberBetween(30, 240),
            'surgery_type' => $this->faker->word(),
            'room' => (string) $this->faker->numberBetween(1, 10),
            'status' => Surgery::STATUS_SCHEDULED,
        ];
    }
}
