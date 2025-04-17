<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\GymEntry;
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
        $query->whereHas('user', function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
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
        default:
            \Log::error("Invalid filter type: {$filter}");
            return back()->with('error', 'Invalid filter type.');
    }

    $attendances = $query->orderBy('time_in', 'desc')
        ->paginate(10)
        ->appends([
            'filter' => $filter,
            'search' => $search,
        ]);

    // If the request is AJAX
    if ($request->ajax()) {
        return response()->json([
            'table' => view('partials.attendance_table', compact('attendances'))->render(),
            'pagination' => view('vendor.pagination.default', ['paginator' => $attendances])->render(),
        ]);        
    }

    return view('staff.attendance', compact('attendances', 'filter', 'search'));
}


    public function timeOut(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'rfid_uid' => 'required|string',
            ]);
    
            $rfid_uid = $request->input('rfid_uid');
    
            // Find the user with an approved session
            $user = User::where('rfid_uid', $rfid_uid)->first();
    
            if (!$user) {
                return back()->with('error', "User not found with RFID: $rfid_uid.");
            }
    
            if ($user->session_status !== 'approved') {
                return back()->with('error', "User $user->first_name is not approved.");
            }
    
            // Find the latest attendance record using RFID UID
            $attendance = Attendance::where('rfid_uid', $rfid_uid)
                ->whereNull('time_out')
                ->latest('time_in')
                ->first();
    
            if (!$attendance) {
                return back()->with('error', "Session Expired - You need to register again.");
            }
    
            // Set the time_out
            $attendance->update(['time_out' => Carbon::now()]);
    
            // ✅ Update session_status and member_status
            $user->update([
                'session_status' => 'pending',
                'member_status' => 'expired',
            ]);
    
            \Log::info("✅ User {$user->first_name} {$user->last_name} (RFID: {$user->rfid_uid}) Time-out recorded at " . now());
    
            return back()->with('success', "✅ Time-out recorded successfully for {$user->first_name}. Membership marked as expired.");
        } catch (\Exception $e) {
            \Log::error("❌ Time-out error: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
  
    // public function getAttendanceDates(Request $request)
    // {
    //     $request->validate([
    //         'rfid_uid' => 'required|string'
    //     ]);
    
    //     $rfidUid = $request->input('rfid_uid');
    
    //     // Get all dates where user has attendance records
    //     $dates = Attendance::where('rfid_uid', $rfidUid)
    //         ->select('attendance_date')
    //         ->distinct()
    //         ->orderBy('attendance_date')
    //         ->pluck('attendance_date')
    //         ->map(function ($date) {
    //             return $date->format('Y-m-d');
    //         });
    
    //     return response()->json($dates);
    // }





}
