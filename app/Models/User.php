<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Staff;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, Billable;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['uuid', 'name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'cart',
        'events',
        'staffs',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function isOrganising(Event $event)
    {
        if ($this->uuid === $event->user_uuid) {
            return true;
        }
        return false;
    }
    public function isStaff(Event $event): bool
    {
        $staffs = $event->staffs; // folosim "staff" deoarece acesta este numele relației

        foreach ($staffs as $staff) {
            if ($staff->user_uuid === $this->uuid) {
                return true;
            }
        }

        return false;
    }

    public function canEdit(Event $event): bool
    {
        $staffUser = $event->staffs->where('user_uuid', $this->uuid)->first();
        if ($staffUser && $staffUser->can_edit) {
            return true;
        }
        return false;
    }
    public function canValidate(Event $event): bool
    {
        $staffUser = $event->staffs->where('user_uuid', $this->uuid)->first();
        if (
            ($staffUser && $staffUser->can_validate) ||
            $this->isOrganising($event)
        ) {
            return true;
        }
        return false;
    }

    public function getCartItemCount(): int
    {
        // Verifică dacă utilizatorul are un coș
        if (!$this->cart) {
            return 0;
        }
        $this->cart->load('items');

        // Întoarce numărul de articole din coș
        return $this->cart->items->count();
    }
}
