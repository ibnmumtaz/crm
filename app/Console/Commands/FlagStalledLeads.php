<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class FlagStalledLeads extends Command
{
    protected $signature = 'crm:flag-stalled-leads';

    protected $description = 'Flag leads with no contact in N days';

    public function handle(): int
    {
        $threshold = now()->subDays(14);
        $count = Lead::where('status', 'active')
            ->where(function ($q) use ($threshold) {
                $q->whereNull('last_contact_at')
                    ->orWhere('last_contact_at', '<', $threshold);
            })
            ->update(['stalled_at' => now()]);
        $this->info("Flagged {$count} stalled leads.");

        return self::SUCCESS;
    }
}
