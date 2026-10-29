<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class FlagOverdueTasks extends Command
{
    protected $signature = 'crm:flag-overdue-tasks';

    protected $description = 'Mark tasks past due as Overdue';

    public function handle(): int
    {
        $count = Task::where('status', 'pending')
            ->where('due_at', '<', now())
            ->update(['status' => 'overdue']);
        $this->info("Flagged {$count} overdue tasks.");

        return self::SUCCESS;
    }
}
