<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class RunBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup {--type=db : Type of backup: db or full}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an automated backup of the clinic system database and files';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type') ?: 'db';
        $this->info("Starting {$type} backup...");

        $result = $backupService->createBackup($type);

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        } else {
            $this->error($result['message']);
            return Command::FAILURE;
        }
    }
}
