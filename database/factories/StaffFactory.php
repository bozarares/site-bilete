<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_uuid' => User::query()->inRandomOrder()->first()->uuid,
            'event_id' => Event::query()->inRandomOrder()->first()->id,
            'can_validate' => fake()->boolean(),
            'can_edit' => fake()->boolean()
        ];
    }
}
