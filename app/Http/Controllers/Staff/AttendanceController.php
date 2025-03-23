<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // For logging

class AttendanceController extends Controller
{
    // Show attendance records
    public function index(Request $request)
    {
        $query = $request->input('search');
    
        // Fetch attendance records with user details, ordered by latest time_in
        $attendances = Attendance::with('user')
        ->when($query, function ($queryBuilder) use ($query) {
            $queryBuilder->whereHas('user', function ($userQuery) use ($query) {
                $userQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                          ->orWhere('first_name', 'like', "%{$query}%")
                          ->orWhere('last_name', 'like', "%{$query}%");
            });
        })
        
            ->orderBy('time_in', 'desc')
            ->paginate(4); // Paginate results (10 per page)
    
        return view('staff.attendance', compact('attendances', 'query'));
    }
    

    // Record attendance when RFID is tapped

    public function recordAttendance(Request $request)
    {
        // Validate the request
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);
    
        // Get the RFID UID from the request
        $rfid_uid = $request->input('rfid_uid');
    
        // Get the current date and time
        $currentDate = Carbon::now()->toDateString();
        $currentTime = Carbon::now();
    
        // Check if the user is registered
        $user = User::where('rfid_uid', $rfid_uid)->first();
        if (!$user) {
            return response()->json(['message' => 'RFID not registered.'], 404);
        }
    
        // Check the member status
        if ($user->member_status === 'expired') {
            return response()->json(['message' => 'Membership expired! Please renew.'], 403);
        }
    
        // Fetch the latest attendance record for today
        $attendance = Attendance::where('rfid_uid', $rfid_uid)
            ->whereDate('time_in', $currentDate)
            ->latest('time_in')
            ->first();
    
        if ($attendance) {
            if ($attendance->time_out === null) {
                // User is checking out
                Log::info('Updating time-out for record ID: ' . $attendance->id);
                $attendance->update(['time_out' => $currentTime]);
    
                return response()->json([
                    'message' => 'Time-out recorded successfully.',
                    'attendance' => $attendance,
                ]);
            } else {
                // Create a new time-in record
                Log::info('Creating new time-in record.');
                $newAttendance = Attendance::create([
                    'rfid_uid' => $rfid_uid,
                    'time_in' => $currentTime,
                    'time_out' => null,
                ]);
    
                // ✅ **Also insert into `gym_entries`**
                GymEntry::create([
                    'rfid_uid' => $rfid_uid,
                    'entry_time' => $currentTime,
                ]);
    
                return response()->json([
                    'message' => 'Time-in recorded successfully.',
                    'attendance' => $newAttendance,
                ]);
            }
        } else {
            // No record exists for today, create a new time-in record
            Log::info('No attendance record found for today. Creating new time-in record.');
            $attendance = Attendance::create([
                'rfid_uid' => $rfid_uid,
                'time_in' => $currentTime,
                'time_out' => null,
            ]);
    
            // ✅ **Also insert into `gym_entries`**
            GymEntry::create([
                'rfid_uid' => $rfid_uid,
                'entry_time' => $currentTime,
            ]);
    
            return response()->json([
                'message' => 'Time-in recorded successfully.',
                'attendance' => $attendance,
            ]);
        }
    }
    
}