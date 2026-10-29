<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['lead_id', 'assignee_id', 'creator_id', 'title', 'notes', 'priority', 'status', 'due_at', 'completed_at', 'outcome'];

    protected $casts = [
        'priority' => TaskPriority::class,
        'status' => TaskStatus::class,
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $query;
        }
        if ($user->hasRole('sales-manager')) {
            $teamUserIds = Team::whereIn('id', $user->teams()->pluck('teams.id'))
                ->with('users')
                ->get()
                ->pluck('users')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->values();

            return $query->where(function ($q) use ($teamUserIds) {
                $q->whereIn('assignee_id', $teamUserIds)
                    ->orWhereIn('creator_id', $teamUserIds);
            });
        }

        return $query->where(function ($q) use ($user) {
            $q->where('assignee_id', $user->id)
                ->orWhere('creator_id', $user->id);
        });
    }
}
