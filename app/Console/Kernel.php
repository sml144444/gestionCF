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
        // Weekly schedule notification to stagiaires
        // Runs every Sunday at 00:00 (midnight → start of Sunday)
        // Only sends if published sessions exist for the group next week
        $schedule->command('schedule:notify-stagiaires')
                 ->weeklyOn(0, '00:00')   // 0 = Sunday
                 ->appendOutputTo(storage_path('logs/schedule-notify.log'));
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