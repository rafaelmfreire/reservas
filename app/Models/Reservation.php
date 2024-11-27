<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'is_confirmed',
        'room_id',
        'responsible',
        'matriculation',
        'sector',
        'phone',
        'email',
        'category',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function dates(): HasMany
    {
        return $this->hasMany(ReservationDate::class);
    }
}
