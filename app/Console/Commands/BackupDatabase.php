<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    protected $process;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (!file_exists(storage_path("backups")) && !mkdir($concurrentDirectory = storage_path("backups"), 0777, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        try {
            $command = sprintf(
                'mysqldump -u%s -p%s %s > %s',
                'forge',
                'fjfj555',
                'digitacao',
                storage_path("backups/backup".now()->format('Y-m-d-H:i:s').".sql")
            );
            exec($command);
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}