<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ticketCategory = $this->resource->ticketCategory;
        $event = $ticketCategory->event;

        $validatorName = '';
        if ($this->validated_by_uuid) {
            $validatorUser = User::where(
                'uuid',
                $this->validated_by_uuid
            )->first();
            $validatorName = optional($validatorUser)->name ?? '';
        }

        $data = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'price' => $this->price,
            'ticket_category_name' => $ticketCategory->name,
            'event_title' => $event->title,
            'event_location' => $event->location,
            'event_datetime' => $event->datetime,
            'validated' => $this->validated,
            'validated_by_name' => $validatorName,
        ];

        return $data;
    }
}
