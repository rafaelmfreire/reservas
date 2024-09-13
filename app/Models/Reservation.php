<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'start_at',
        'end_at',
        'responsible_id'
    ];

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Responsible::class);
    }
}
