<?php

namespace Database\Factories;

use App\Models\EventType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
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
            "user_uuid" => User::query()->inRandomOrder()->first()->uuid,
            "title" => fake()->sentence(5),
            'event_type_id' => EventType::query()->inRandomOrder()->first()->id,
            "location" => fake()->address(),
            "start_date" => $startDate,
            "end_date" => $endDate,
            "description" => fake()->sentences(3, true)
        ];
    }
}
