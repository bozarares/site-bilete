<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $event = $this->resource->event;
        $data = [
            'id' => $this->staff_id,
            'event_id' => $event->event_id,
            'user_uuid' => $this->user_uuid,
            'can_validate' => $this->can_validate,
            'can_edit' => $this->can_edit,
        ];

        return $data;
    }
}
