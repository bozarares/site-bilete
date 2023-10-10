<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Event;
use Vinkla\Hashids\Facades\Hashids;

class Staff extends Model
{
    use HasFactory;
    // protected $primaryKey = 'uuid';
    // protected $keyType = 'string';
    // public $incrementing = false;
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         $model->{$model->getKeyName()} = (string) Str::uuid();
    //     });
    // }
    // public function getKeyType()
    // {
    //     return 'string';
    // }

    public function setStaffIdAttribute($value)
    {
        $hashids = Hashids::connection('staff');
        $decoded = $hashids->decode(strval($value));
        $this->attributes['id'] = isset($decoded[0]) ? $decoded[0] : null;
    }

    // Accesor pentru ID
    public function getStaffIdAttribute()
    {
        $hashids = Hashids::connection('staff');
        return $hashids->encode($this->attributes['id']);
    }

    protected $fillable = ['event_id', 'user_uuid', 'can_validate', 'can_edit'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
