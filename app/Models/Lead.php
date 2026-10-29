<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id', 'team_id', 'pipeline_id', 'stage_id', 'source_id',
        'name', 'company', 'email', 'phone', 'value', 'currency',
        'expected_close', 'status', 'lost_reason', 'last_contact_at', 'stalled_at',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
        'expected_close' => 'date',
        'last_contact_at' => 'datetime',
        'stalled_at' => 'datetime',
        'value' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(LeadStageHistory::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $query;
        }
        if ($user->hasRole('sales-manager')) {
            $teamIds = $user->teams()->pluck('teams.id');

            return $query->whereIn('team_id', $teamIds);
        }

        return $query->where('owner_id', $user->id);
    }
}
