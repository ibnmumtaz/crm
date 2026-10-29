<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'name', 'company', 'email', 'phone'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class, 'lead_id', 'lead_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $query;
        }

        $teamIds = $user->teams()->pluck('teams.id');

        return $query->where(function ($q) use ($user, $teamIds) {
            $q->whereNull('lead_id')
                ->orWhereHas('lead', function ($leadQuery) use ($user, $teamIds) {
                    $leadQuery->where('owner_id', $user->id)
                        ->orWhereIn('team_id', $teamIds);
                });
        });
    }
}
