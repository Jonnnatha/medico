<?php

namespace Database\Factories;

use App\Models\Surgery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<\App\Models\Surgery>
 */
class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition(): array
    {
        $starts = Carbon::instance($this->faker->dateTimeBetween('-1 week', '+1 week'));
        $duration = $this->faker->numberBetween(30, 180);
        $ends = (clone $starts)->addMinutes($duration);

        return [
            'patient_name' => $this->faker->name(),
            'surgery_type' => $this->faker->word(),
            'room' => $this->faker->randomElement(['A', 'B', 'C']),
            'starts_at' => $starts,
            'duration_min' => $duration,
            'ends_at' => $ends,
            'status' => 'scheduled',
            'is_conflict' => false,
            'created_by' => User::factory(),
            'confirmed_by' => null,
            'canceled_by' => null,
        ];
    }
}
