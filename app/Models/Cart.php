<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected static function booted()
    {
        static::deleting(function ($cart) {
            $cart->items()->delete();
        });
    }
    protected $fillable = ['user_uuid'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalPriceAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->ticketCategory->price * $item->quantity;
        });
    }
}
