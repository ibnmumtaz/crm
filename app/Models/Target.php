<?php

namespace App\Models;

use App\Enums\TargetPeriod;
use App\Enums\TargetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'team_id', 'type', 'period', 'period_start', 'period_end', 'amount', 'created_by'];

    protected $casts = [
        'type' => TargetType::class,
        'period' => TargetPeriod::class,
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $query;
        }
        $teamIds = $user->teams()->pluck('teams.id');

        return $query->where(function ($q) use ($user, $teamIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('team_id', $teamIds)
                ->orWhere('created_by', $user->id);
        });
    }
}
