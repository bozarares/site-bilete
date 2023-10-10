<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Staff;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();

        $eventTypeNames = ['Festival', 'Concert', 'Stand Up'];
        $eventTypeIds = [];

        foreach ($eventTypeNames as $name) {
            $eventType = \App\Models\EventType::factory()->create([
                'name' => $name,
            ]);
            $eventTypeIds[] = $eventType->id;
        }

        Event::factory(19)->create();
        \App\Models\TicketCategory::factory(40)->create();
        Staff::factory(8)->create();

        $organiser = User::factory()->create([
            'name' => 'Adrian Ionescu',
            'email' => 'organiser@example.com',
        ]);

        $staffValidate = User::factory()->create([
            'name' => 'Gabriela Popescu',
            'email' => 'staff_validate@example.com',
        ]);

        $staffEdit = User::factory()->create([
            'name' => 'Mihai Vasilescu',
            'email' => 'staff_edit@example.com',
        ]);

        $event = Event::factory()->create([
            'title' => 'Festival de muzică',
            'user_uuid' => $organiser->uuid,
            'event_type_id' => $eventTypeIds[0],
        ]);

        \App\Models\TicketCategory::factory()->create([
            'event_id' => $event->id,
            'name' => 'Categorie 1',
            'price' => 10,
            'total_tickets' => 100,
        ]);
        \App\Models\TicketCategory::factory()->create([
            'event_id' => $event->id,
            'name' => 'Categorie 2',
            'price' => 20,
            'total_tickets' => 50,
        ]);

        // Create staff_validate with validate permissions
        Staff::factory()->create([
            'user_uuid' => $staffValidate->uuid,
            'event_id' => $event->id,
            'can_validate' => true,
            'can_edit' => false,
        ]);

        // Create staff_edit with edit permissions
        Staff::factory()->create([
            'user_uuid' => $staffEdit->uuid,
            'event_id' => $event->id,
            'can_validate' => false,
            'can_edit' => true,
        ]);
    }
}
