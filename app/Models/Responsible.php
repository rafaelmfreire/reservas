<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responsible extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'matriculation',
        'category',
        'sector',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
