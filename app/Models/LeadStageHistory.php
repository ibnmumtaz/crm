<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStageHistory extends Model
{
    use HasFactory;

    protected $table = 'lead_stage_history';

    protected $fillable = ['lead_id', 'from_stage_id', 'to_stage_id', 'user_id', 'reason'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'from_stage_id');
    }

    public function toStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'to_stage_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
