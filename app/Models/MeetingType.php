<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'duration_minutes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
