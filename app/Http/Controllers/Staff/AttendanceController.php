<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\GymEntry; // Make sure to import GymEntry
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(Request $request)
{
    $query = Attendance::with('user');
    
    // Search filter
    $search = $request->input('search', '');
    if (!empty($search)) {
        $query->whereHas('user', function($q) use ($search) {
            $q->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        });
    }

    // Time period filter
    $filter = $request->input('filter', 'today');
    switch ($filter) {
         case 'today':
            $query->whereDate('time_in', Carbon::today());
            break;
        case 'yesterday':
            $query->whereDate('time_in', Carbon::yesterday());
            break;
        case 'thisWeek':
            $query->whereBetween('time_in', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
            break;
        case 'lastWeek':
            $query->whereBetween('time_in', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ]);
            break;
        case 'thisMonth':
            $query->whereBetween('time_in', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ]);
            break;
    }

    // Always order by time_in descending (newest first)
    $attendances = $query->orderBy('time_in', 'desc')
        ->paginate(10)
        ->appends([
            'filter' => $filter,
            'search' => $search,
        ]);

    return view('staff.attendance', compact('attendances', 'filter', 'search'));
}

public function recordAttendance(Request $request)
{
    // Validate request inputs
    $request->validate([
        'rfid_uid' => 'nullable|string',  // RFID UID is optional for QR-based check-ins
        'email' => 'nullable|email|exists:users,email',  // Email is required for QR-based check-ins
    ]);

    $currentTime = Carbon::now();
    $user = null;

    // Check if the request is for an RFID check-in
    if ($request->filled('rfid_uid')) {
        $rfid_uid = $request->input('rfid_uid');
        $user = User::where('rfid_uid', $rfid_uid)->first();
    }

    // Check if the request is for a QR-based check-in (by email)
    if ($request->filled('email')) {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
    }

    // If user is not found, return error
    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    // Ensure that the user has an approved session for QR-based members
    if ($user->session_status !== 'approved') {
        return response()->json(['message' => 'Session membership not approved!'], 403);
    }

    // Check for open attendance (time_in without time_out)
    $openAttendance = Attendance::where('user_id', $user->id)
        ->whereNull('time_out')
        ->latest('time_in')
        ->first();

    if ($openAttendance) {
        // User is checking out, calculate time difference
        $time_in = Carbon::parse($openAttendance->time_in);
        $time_diff = $currentTime->diffInSeconds($time_in);
        $min_time_difference = 30; // Minimum time between time-in and time-out

        if ($time_diff < $min_time_difference) {
            // User must wait for the minimum time difference before checking out
            return response()->json(['message' => 'Please wait at least ' . $min_time_difference . ' seconds before checking out.'], 400);
        }

        // Proceed to check out and record time-out
        $openAttendance->update(['time_out' => $currentTime]);

        // Optional: Log time-out
        Log::info("User {$user->first_name} {$user->last_name} (UID: {$user->rfid_uid}) Time-out recorded at {$currentTime}");

        return response()->json([
            'message' => 'Time-out recorded successfully.',
            'attendance' => $openAttendance,
        ]);
    } else {
        // User is checking in
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'time_in' => $currentTime,
            'time_out' => null,
        ]);

        // Create Gym Entry record for tracking
        GymEntry::create([
            'user_id' => $user->id,
            'entry_time' => $currentTime,
        ]);

        // Optional: Log time-in
        Log::info("User {$user->first_name} {$user->last_name} (UID: {$user->rfid_uid}) Time-in recorded at {$currentTime}");

        return response()->json([
            'message' => 'Time-in recorded successfully.',
            'attendance' => $attendance,
        ]);
    }
}

}