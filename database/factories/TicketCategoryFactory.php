<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 month', '+1 year');
        $endDate = (clone $startDate)->modify('+' . rand(1, 5) . ' days');
        return [
            'event_id' => Event::query()->inRandomOrder()->first()->id,
            'paused' => 0,
            'name' => fake()->word(),
            'description' => fake()->sentence(3, true),
            'price' => fake()->numberBetween(25, 300),
            'total_tickets' => fake()->numberBetween(50, 300),
            "start_date" => $startDate,
            "end_date" => $endDate,
        ];
    }
}
