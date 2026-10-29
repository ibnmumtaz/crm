<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['lead_id', 'user_id', 'type', 'subject', 'body', 'outcome', 'occurred_at', 'editable_until'];

    protected $casts = [
        'occurred_at' => 'datetime',
        'editable_until' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $query;
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');
            $leadIds = Lead::whereIn('team_id', $teamIds)->pluck('id');

            return $query->where(function ($q) use ($user, $leadIds) {
                $q->whereIn('lead_id', $leadIds)->orWhere('user_id', $user->id);
            });
        }

        $leadIds = Lead::where('owner_id', $user->id)->pluck('id');

        return $query->where(function ($q) use ($user, $leadIds) {
            $q->whereIn('lead_id', $leadIds)->orWhere('user_id', $user->id);
        });
    }
}
