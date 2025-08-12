<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Test scheduler - runs every minute for testing
        $schedule->command('test:scheduler')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();

        // You can add more scheduled tasks here:
        
        // Example: Send daily digest emails
        // $schedule->job(new SendDailyDigest())->daily();
        
        // Example: Clean up old logs
        // $schedule->command('logs:cleanup')->weekly();
        
        // Example: Generate reports
        // $schedule->command('reports:generate')->weeklyOn(1, '8:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}