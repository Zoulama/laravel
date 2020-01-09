<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendScaleAlertInfo',
        'App\Console\Commands\SendScaleInfos',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $logFile = storage_path() . '/logs/cron.log';

        $schedule
            ->command('send-email:infos')
            ->sendOutputTo($logFile)
            ->withoutOverlapping()
            ->cron('0 8 * * *');

        $schedule
            ->command('send-email:alert ')
            ->sendOutputTo($logFile)
            ->withoutOverlapping()
            ->cron('*/30 * * * *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
