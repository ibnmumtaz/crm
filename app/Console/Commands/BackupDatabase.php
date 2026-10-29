<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'crm:backup-database';

    protected $description = 'Backup the SQLite database';

    public function handle(): int
    {
        $source = config('database.connections.sqlite.database');
        $target = storage_path('backups/crm-'.now()->format('Ymd-His').'.sqlite');

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        $pdo = new \PDO('sqlite:'.$source);
        $pdo->exec("VACUUM INTO '{$target}'");

        $this->info("Backup created: {$target}");

        return self::SUCCESS;
    }
}
