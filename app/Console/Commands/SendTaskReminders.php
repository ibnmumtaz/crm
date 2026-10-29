<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDue;
use App\Notifications\TaskOverdue;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    protected $signature = 'crm:send-task-reminders';

    protected $description = 'Notify assignees of due/overdue tasks';

    public function handle(): int
    {
        $dueSoon = Task::where('status', 'pending')
            ->whereBetween('due_at', [now(), now()->addHour()])
            ->get();
        foreach ($dueSoon as $task) {
            $task->assignee->notify(new TaskDue($task));
        }

        $overdue = Task::where('status', 'overdue')->get();
        foreach ($overdue as $task) {
            $task->assignee->notify(new TaskOverdue($task));
        }

        $this->info('Sent reminders.');

        return self::SUCCESS;
    }
}
