<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCheckoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check out all active gym attendees at 9 PM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Running auto-checkout at {$now}");

        $activeAttendances = Attendance::whereNull('time_out')
            ->with('user')
            ->get();

        $count = 0;
        foreach ($activeAttendances as $attendance) {
            $attendance->time_out = Carbon::now();
            $attendance->save();
            
            // // Update user status
            // if ($attendance->user) {
            //     $attendance->user->update([
            //         'session_status' => 'pending',
            //         'member_status' => 'expired',
            //     ]);
            //     $count++;
            // }
        }

        Log::info("Auto-checkout completed: {$count} users checked out automatically");
        $this->info("Successfully checked out {$count} users");
    }
}