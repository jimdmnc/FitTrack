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
        // Fetch attendance records with user details, ordered by latest time_in
        $attendances = Attendance::with('user')
            ->orderBy('time_in', 'desc')
            ->paginate(10); // Paginate results (10 per page)

        return view('staff.attendance', compact('attendances'));
    }

    // Record attendance when RFID is tapped
    public function recordAttendance(Request $request)
    {
        // Validate the request
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);

        // Get the UID from the request
        $rfid_uid = $request->input('rfid_uid');

        // Get the current date and time
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now();

        // Check if the user is registered
        $user = User::where('rfid_uid', $rfid_uid)->first();

        if (!$user) {
            return response()->json([
                'message' => 'RFID not registered.',
            ], 404);
        }

        // Check if there's an existing attendance record for today
        $attendance = Attendance::where('rfid_uid', $rfid_uid)
            ->whereDate('time_in', $currentDate)
            ->latest() // Get the latest record for the day
            ->first();

        if ($attendance) {
            // If there's a time-in record but no time-out, update time-out
            if (!$attendance->time_out) {
                $attendance->update(['time_out' => $currentTime]);

                return response()->json([
                    'message' => 'Time-out recorded successfully.',
                    'attendance' => $attendance,
                ]);
            } else {
                // If both time-in and time-out are recorded, create a new time-in record
                $newAttendance = Attendance::create([
                    'rfid_uid' => $rfid_uid,
                    'time_in' => $currentTime,
                    'time_out' => null,
                ]);

                return response()->json([
                    'message' => 'Time-in recorded successfully.',
                    'attendance' => $newAttendance,
                ]);
            }
        } else {
            // If no attendance record exists for today, create a new time-in record
            $attendance = Attendance::create([
                'rfid_uid' => $rfid_uid,
                'time_in' => $currentTime,
                'time_out' => null,
            ]);

            return response()->json([
                'message' => 'Time-in recorded successfully.',
                'attendance' => $attendance,
            ]);
        }
    }
}