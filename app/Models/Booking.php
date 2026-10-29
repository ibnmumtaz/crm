<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['meeting_type_id', 'name', 'email', 'company', 'notes', 'starts_at', 'ends_at', 'timezone', 'status', 'cancellation_token'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }
}
