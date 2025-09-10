<?php

namespace Database\Factories;

use App\Models\Surgery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Surgery>
 */
class SurgeryFactory extends Factory
{
    protected $model = Surgery::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+0 days', '+1 week');

        return [
            'room_id' => $this->faker->numberBetween(1, 5),
            'starts_at' => $start,
            'duration_min' => $this->faker->numberBetween(30, 240),
        ];
    }
}
