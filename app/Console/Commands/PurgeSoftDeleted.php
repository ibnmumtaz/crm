<?php

namespace App\Console\Commands;

use App\Models\Communication;
use App\Models\Lead;
use App\Models\Task;
use Illuminate\Console\Command;

class PurgeSoftDeleted extends Command
{
    protected $signature = 'crm:purge-soft-deleted';

    protected $description = 'Permanently delete records past retention';

    public function handle(): int
    {
        $threshold = now()->subDays(90);
        Lead::onlyTrashed()->where('deleted_at', '<', $threshold)->forceDelete();
        Task::onlyTrashed()->where('deleted_at', '<', $threshold)->forceDelete();
        Communication::onlyTrashed()->where('deleted_at', '<', $threshold)->forceDelete();
        $this->info('Purged soft-deleted records.');

        return self::SUCCESS;
    }
}
