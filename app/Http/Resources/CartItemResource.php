<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ticektCategory = $this->ticketCategory;
        $event = $ticektCategory->event;
        $data = [
            'id' => $this->id,
            'event_name' => $event->title,
            'name' => $ticektCategory->name,
            'price' => $ticektCategory->price,
            'quantity' => $this->quantity,
        ];
        return $data;
    }
}
