<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show attendance records
    public function index()
    {
        $attendances = Attendance::with('user')->latest()->paginate(10);
        return view('staff.attendance', compact('attendances'));
    }

    // Record attendance when RFID is tapped
    public function recordAttendance(Request $request)
    {
        $rfid_uid = $request->input('rfid_uid');
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now();

        // Check if RFID exists in users table
        $user = User::where('rfid_uid', $rfid_uid)->first();
        if (!$user) {
            return response()->json(['message' => 'RFID not registered'], 404);
        }

        // Check if user has already timed in today
        $attendance = Attendance::where('rfid_uid', $rfid_uid)
                                ->where('date', $currentDate)
                                ->first();

        if ($attendance) {
            // If already timed in, mark time_out
            if (!$attendance->time_out) {
                $attendance->update(['time_out' => $currentTime]);
                return response()->json(['message' => 'Time-out recorded'], 200);
            } else {
                return response()->json(['message' => 'Already timed in and out today'], 400);
            }
        } else {
            // If first tap, mark time_in
            Attendance::create([
                'rfid_uid' => $rfid_uid,
                'date' => $currentDate,
                'time_in' => $currentTime,
            ]);
            return response()->json(['message' => 'Time-in recorded'], 200);
        }
    }
}
