<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command to verify scheduler is working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        
        // Log to Laravel log
        \Log::info("Scheduler test command executed at: {$timestamp}");
        
        // Also create a simple file to track executions
        $logFile = storage_path('logs/scheduler-test.log');
        file_put_contents($logFile, "[{$timestamp}] Scheduler is working!\n", FILE_APPEND | LOCK_EX);
        
        $this->info("Scheduler test executed successfully at {$timestamp}");
        
        return 0;
    }
}
