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
        if (!$user) {
            Log::error("No authenticated user found for manual attendance page.");
            return redirect()->route('login')->with('error', 'Please log in to access manual attendance.');
        }

        $uid = $user->rfid_uid;
        $current_time = Carbon::now('Asia/Manila');

        Log::info("Displaying manual attendance page for user: {$user->first_name} (UID: {$uid}) at {$current_time}");

        try {
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            return view('manual_attendance', [
                'attendance' => $attendance,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching attendance data for UID {$uid}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while loading the manual attendance page.');
        }
    }

    public function manualTimeIn(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error("No authenticated user found for manual time-in.");
            return redirect()->route('login')->with('error', 'Please log in to record time-in.');
        }

        $uid = $user->rfid_uid;
        $current_time = Carbon::now('Asia/Manila');

        Log::info("Processing manual time-in for user: {$user->first_name} (UID: {$uid}) at {$current_time}");

        DB::beginTransaction();

        try {
            if ($user->member_status === 'expired') {
                Log::warning("Membership expired for UID: {$uid}");
                return redirect()->back()->with('error', 'Membership expired! Attendance not recorded.');
            }

            if ($user->member_status === 'revoked') {
                Log::warning("Membership revoked for UID: {$uid}");
                return redirect()->back()->with('error', 'Membership revoked! Attendance not recorded.');
            }

            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            if ($attendance && !$attendance->time_out) {
                Log::info("User {$user->first_name} (UID: {$uid}) already timed in today");
                return redirect()->back()->with('error', 'You have already timed in today.');
            }

            if ($attendance && $attendance->time_out) {
                Log::info("User {$user->first_name} (UID: {$uid}) already timed out today");
                return redirect()->back()->with('error', 'You have already timed out today.');
            }

            DB::table('attendances')->insert([
                'rfid_uid' => $uid,
                'time_in' => $current_time,
                'attendance_date' => $current_time->toDateString(),
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ]);

            DB::commit();
            Log::info("User {$user->first_name} (UID: {$uid}) Manual time-in recorded at {$current_time}");
            return redirect()->back()->with('success', 'Time-in recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing manual time-in for UID {$uid}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while recording time-in.');
        }
    }

    public function manualTimeOut(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error("No authenticated user found for manual time-out.");
            return redirect()->route('login')->with('error', 'Please log in to record time-out.');
        }

        $uid = $user->rfid_uid;
        $current_time = Carbon::now('Asia/Manila');

        Log::info("Processing manual time-out for user: {$user->first_name} (UID: {$uid}) at {$current_time}");

        DB::beginTransaction();

        try {
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            if (!$attendance || $attendance->time_out) {
                Log::info("No active session for user {$user->first_name} (UID: {$uid})");
                return redirect()->back()->with('error', 'No active session to time out.');
            }

            DB::table('attendances')
                ->where('id', $attendance->id)
                ->update([
                    'time_out' => $current_time,
                    'updated_at' => $current_time,
                ]);

            DB::commit();
            Log::info("User {$user->first_name} (UID: {$uid}) Manual time-out recorded at {$current_time}");
            return redirect()->back()->with('success', 'Time-out recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing manual time-out for UID {$uid}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while recording time-out.');
        }
    }
}
?>