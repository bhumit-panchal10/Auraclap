<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Register your commands here
    protected $commands = [
        \App\Console\Commands\SendScheduledRideNotification::class,
        // Add other commands here if needed
    ];
    
    protected function schedule(Schedule $schedule)
    {
        // Schedule the command to run every minute or at intervals as needed
        // $schedule->command('send:scheduled-notifications')->everyMinute();
        // $schedule->command('send:scheduled-notifications')->everyMinute()->appendOutputTo(storage_path('logs/scheduled.log'));
        $schedule->command('send:scheduled-notifications')->everyMinute()->withoutOverlapping();


        
        $schedule->command('test:cron')->everyMinute(); // Add this line
    }
}
