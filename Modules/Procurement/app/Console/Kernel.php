<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // check low stock hourly
        $schedule->command('inventory:check-low-stock --threshold=5')->hourly();

        // auto-flag deliveries whose expected date has passed as delayed
        $schedule->command('deliveries:mark-delayed')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
