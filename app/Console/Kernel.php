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
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:check-over-demurrage-cron')
            ->dailyAt('00:10')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:sync-new-container-data-cron')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:sync-new-si-data-cron')
            ->cron('*/55 * * * *')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:sync-update-container-data-cron')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:sync-update-si-data-cron')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:sync-new-timbangan-data-cron')
            ->everyTwoHours()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('app:input-container-history-cron')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();
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
