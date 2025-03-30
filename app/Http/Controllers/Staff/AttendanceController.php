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
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);
    
        $rfid_uid = $request->input('rfid_uid');
        $currentTime = Carbon::now();
    
        $user = User::where('rfid_uid', $rfid_uid)->first();
        
        if (!$user) {
            return response()->json(['message' => 'RFID not registered.'], 404);
        }
    
        if ($user->member_status === 'expired') {
            return response()->json(['message' => 'Membership expired! Please renew.'], 403);
        }
    
        // Check for open attendance (time_in without time_out)
        $openAttendance = Attendance::where('rfid_uid', $rfid_uid)
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();
    
        if ($openAttendance) {
            // User is checking out
            $openAttendance->update(['time_out' => $currentTime]);
            
            return response()->json([
                'message' => 'Time-out recorded successfully.',
                'attendance' => $openAttendance,
            ]);
        } else {
            // User is checking in
            $attendance = Attendance::create([
                'rfid_uid' => $rfid_uid,
                'time_in' => $currentTime,
                'time_out' => null,
            ]);
    
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