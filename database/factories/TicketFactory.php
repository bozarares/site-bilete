<?php

namespace Database\Factories;

use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ticketCategory = TicketCategory::query()->inRandomOrder()->first();
        $user = User::query()->inRandomOrder()->first();
        return [
            'ticket_category_id' => $ticketCategory->id,
            'user_uuid' => $user->uuid,
            'price' => $ticketCategory->price,
            'name' => $user->name,
            'validated' => 0,
            'validated_by_uuid' => ""
        ];
    }
}
