<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManualAttendanceController extends Controller
{

    public function showManualAttendance(Request $request)
    {
        $user = Auth::user();
        $uid = $user->rfid_uid;
        $current_time = Carbon::now('Asia/Manila');

        Log::info("Displaying manual attendance page for user: {$user->first_name} (UID: {$uid}) at {$current_time}");

        try {
            // Check for active attendance record (time-in without time-out for today)
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            // Pass attendance data to the view
            return view('manual_attendance', [
                'attendance' => $attendance,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching attendance data for UID {$uid}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while loading the manual attendance page.');
        }
    }
}
?>