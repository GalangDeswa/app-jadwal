<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartQueueListener extends Command
{
    protected $signature = 'queue:start-listener';
    protected $description = 'Start the queue listener';

    public function handle()
    {
        // Start the queue listener process
        $process = new Process(['php', 'artisan', 'queue:work', '--timeout=0']);
        $process->setTimeout(null); // Disable timeout
        $process->start();

        $this->info('Queue listener started.');
    }
}