<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class TicketCategory extends Model
{
    use HasFactory;

    public function setTicketCategoryIdAttribute($value)
    {
        $hashids = Hashids::connection('ticket_category');
        $decoded = $hashids->decode(strval($value));
        $this->attributes['id'] = isset($decoded[0]) ? $decoded[0] : null;
    }

    // Accesor pentru ID
    public function getTicketCategoryIdAttribute()
    {
        $hashids = Hashids::connection('ticket_category');
        return $hashids->encode($this->attributes['id']);
    }
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'total_tickets',
        'remaining_tickets',
        'paused',
        'start_date',
        'end_date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function categoryRevenue()
    {
        return $this->tickets()->sum('price');
    }
    public function getBoughtTickets()
    {
        $bought_tickets = Ticket::where(
            'ticket_category_id',
            $this->id
        )->count();
        return $bought_tickets;
    }
}
