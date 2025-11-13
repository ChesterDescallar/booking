<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+30 days');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(1, 4) . ' hours');

        return [
            'user_id' => \App\Models\User::factory(),
            'client_id' => \App\Models\Client::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
