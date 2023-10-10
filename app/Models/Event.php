<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TicketCategory;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Event extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    // ID Accesor and Mutator
    public function setEventIdAttribute($value)
    {
        $hashids = Hashids::connection('events');
        $decoded = $hashids->decode(strval($value));
        $this->attributes['id'] = isset($decoded[0]) ? $decoded[0] : null;
    }
    public function getEventIdAttribute()
    {
        $hashids = Hashids::connection('events');
        return $hashids->encode($this->attributes['id']);
    }

    protected $fillable = [
        'user_uuid',
        'title',
        'event_type_id',
        'location',
        'description',
        'tickets',
        'staff',
        'start_date',
        'end_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tickets' => 'string',
        'staff' => 'string',
    ];

    // public function organiser()
    // {
    //     return $this->belongsTo(User::class, "user_uuid");
    // }

    // public function ticketCategories()
    // {
    //     return $this->hasMany(TicketCategory::class);
    // }
    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function tickets()
    {
        return $this->hasManyThrough(
            Ticket::class, // The model of the final result - Ticket
            TicketCategory::class, // The intermediate model - TicketCategory
            'event_id', // Foreign key on the intermediate model
            'ticket_category_id', // Foreign key on the final model
            'id', // Local key on this model
            'id' // Local key on the intermediate model
        );
    }
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
}
