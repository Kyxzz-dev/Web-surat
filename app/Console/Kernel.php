<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DeleteUnverifiedUsers;

class Kernel extends ConsoleKernel
{
    // ✅ Daftarkan custom command
    protected $commands = [
        DeleteUnverifiedUsers::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('users:delete-unverified')->everyTenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
