<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TicketCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $category_revenue = 'Not organiser';
        $boughtTickets = $this->getBoughtTickets();
        $event = $this->resource->event;
        $data = [
            'id' => $this->ticket_category_id,
            'event_id' => $event->event_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'total_tickets' => $this->total_tickets,
            'bought_tickets' => $boughtTickets,
            'remaining_tickets' => $this->total_tickets - $boughtTickets,
            'category_revenue' => $category_revenue,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'paused' => $this->paused,
        ];
        if ($user && $user->isOrganising($event)) {
            $data['category_revenue'] = $this->categoryRevenue();
        }
        return $data;
    }
}
