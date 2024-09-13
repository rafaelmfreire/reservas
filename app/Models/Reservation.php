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
        'responsible_id',
        'room_id'
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Responsible::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function dates(): HasMany
    {
        return $this->hasMany(ReservationDate::class);
    }
}
