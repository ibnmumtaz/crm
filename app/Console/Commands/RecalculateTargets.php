<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Target;
use Illuminate\Console\Command;

class RecalculateTargets extends Command
{
    protected $signature = 'crm:recalculate-targets';

    protected $description = 'Refresh target progress aggregates';

    public function handle(): int
    {
        $count = 0;

        foreach (Target::all() as $target) {
            $actual = Lead::query()
                ->when($target->user_id, fn ($q) => $q->where('owner_id', $target->user_id))
                ->where('status', 'won')
                ->whereDate('updated_at', '>=', $target->period_start)
                ->whereDate('updated_at', '<=', $target->period_end)
                ->sum('value');

            cache()->put("target:{$target->id}:progress", (float) $actual, now()->addHour());
            $count++;
        }

        $this->info("Recalculated {$count} targets.");

        return self::SUCCESS;
    }
}
