<?php

namespace App\Console;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $today = Carbon::today();
            $autoCheckoutTime = Carbon::today()->setTime(21, 0, 0);

            Attendance::whereNull('time_out')
                ->whereDate('time_in', $today)
                ->update([
                    'time_out' => $autoCheckoutTime,
                    'status' => 'completed',
                ]);
        })->dailyAt('21:00');

        
        // New schedule for notifications (e.g., daily at 8 AM)
        $schedule->command('notifications:send-automated')
        ->dailyAt('11:47'); // Adjust time as needed (PST)

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