<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('crm:flag-overdue-tasks')->everyFifteenMinutes();
Schedule::command('crm:send-task-reminders')->everyFifteenMinutes();
Schedule::command('crm:flag-stalled-leads')->dailyAt('06:00');
Schedule::command('crm:recalculate-targets')->hourly();
Schedule::command('crm:purge-soft-deleted')->dailyAt('03:00');
Schedule::command('crm:backup-database')->dailyAt('02:00');
Schedule::command('crm:send-weekly-digest')->weeklyOn(1, '07:00');
