<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\GymEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Automatically checkout sessions from previous days
        $this->checkoutPastSessions();
    
        // Begin query and join users to allow ordering by users.end_date
        $query = Attendance::select('attendances.*')
            ->join('users', 'users.rfid_uid', '=', 'attendances.rfid_uid')
            ->with('user');
    
        // Search filter
        $search = $request->input('search', '');
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('users.first_name', 'like', "%{$search}%")
                    ->orWhere('users.last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$search}%"]);
            });
        }
    
        // Time period filter
        $filter = $request->input('filter', 'all');
        switch ($filter) {
            case 'today':
                $query->whereDate('attendances.time_in', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('attendances.time_in', Carbon::yesterday());
                break;
            case 'thisWeek':
                $query->whereBetween('attendances.time_in', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'lastWeek':
                $query->whereBetween('attendances.time_in', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
                break;
            case 'thisMonth':
                $query->whereBetween('attendances.time_in', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                break;
            case 'all':
                // No date filtering needed
                break;
            default:
                $filter = 'all';
        }
    
        // Order by end_date from users table
        $attendances = $query->orderBy('users.end_date', 'desc')
            ->paginate(10)
            ->appends([
                'filter' => $filter,
                'search' => $search,
            ]);
    
        if ($request->ajax()) {
            return response()->json([
                'table' => view('partials.attendance_table', compact('attendances'))->render(),
                'pagination' => $attendances->links('vendor.pagination.default')->render(),
            ]);
        }
    
        return view('staff.attendance', compact('attendances', 'filter', 'search'));
    }
    
    /**
     * Check out all sessions from previous days
     */
    private function checkoutPastSessions()
    {
        $yesterday = Carbon::yesterday()->endOfDay();
        $today9PM = Carbon::today()->setTime(21, 0, 0); // 9 PM today
        
        // Check out yesterday's sessions
        $pastSessions = Attendance::whereNull('time_out')
            ->where('time_in', '<', $yesterday)
            ->with('user')
            ->get();
            
        foreach ($pastSessions as $session) {
            $checkoutTime = Carbon::parse($session->time_in)->setTime(21, 0, 0);
            $session->time_out = $checkoutTime;
            $session->save();
            
            if ($session->user) {
                $session->user->update([
                    'session_status' => 'pending',
                    'member_status' => 'expired',
                ]);
            }
        }
        
        // Check out today's sessions if it's after 9 PM
        if (Carbon::now()->gte($today9PM)) {
            $todaysSessions = Attendance::whereNull('time_out')
                ->whereDate('time_in', Carbon::today())
                ->with('user')
                ->get();
                
            foreach ($todaysSessions as $session) {
                $session->time_out = $today9PM;
                $session->save();
                
                if ($session->user) {
                    $session->user->update([
                        'session_status' => 'pending',
                        'member_status' => 'expired',
                    ]);
                }
            }
        }
        
        Log::info("Fixed past sessions without checkout");
    }

    /**
     * Record a timeout for the user
     */
    public function timeout(Request $request)
    {
        try {
            $rfidUid = $request->input('rfid_uid');
            
            // Find the latest attendance record for this user
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $rfidUid)
                ->whereNull('time_out')
                ->orderBy('time_in', 'desc')
                ->first();
            
            if ($attendance) {
                // Update the attendance record with time_out
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update([
                        'time_out' => Carbon::now(),
                    ]);
                
                // Set a session flag that the user has timed out
                session(['timed_out' => true]);
                
                // Return JSON response for AJAX requests
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Timed out successfully',
                        'timed_out' => true
                    ]);
                }
                
                // For non-AJAX requests, redirect back with success message
                return redirect()->back()->with('success', 'You have successfully timed out.');
            }
            
            // No active attendance found
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active session found'
                ]);
            }
            
            return redirect()->back()->with('error', 'No active session found.');
            
        } catch (\Exception $e) {
            logger()->error('Attendance Timeout Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing timeout: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Error processing timeout: ' . $e->getMessage());
        }
    }

    /**
     * Record a check-in for the user
     */
    public function checkin(Request $request)
    {
        try {
            $rfidUid = $request->input('rfid_uid');
            
            // Check if there's already an active attendance record
            $activeAttendance = DB::table('attendances')
                ->where('rfid_uid', $rfidUid)
                ->whereNull('time_out')
                ->whereDate('time_in', today())
                ->first();
                
            if ($activeAttendance) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have an active session'
                    ]);
                }
                
                return redirect()->back()->with('error', 'You already have an active session.');
            }
            
            // Create new attendance record
            $attendanceId = DB::table('attendances')->insertGetId([
                'rfid_uid' => $rfidUid,
                'time_in' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Get the created attendance record
            $attendance = DB::table('attendances')->find($attendanceId);
            
            // Clear the timed_out flag
            session()->forget('timed_out');
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Checked in successfully',
                    'attendance' => $attendance
                ]);
            }
            
            return redirect()->back()->with('success', 'You have successfully checked in.');
            
        } catch (\Exception $e) {
            logger()->error('Attendance Check-in Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing check-in: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Error processing check-in: ' . $e->getMessage());
        }
    }

    
    public function timeIn(Request $request)
{
    try {
        // Validate input
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);

        $rfid_uid = $request->input('rfid_uid');

        // Find the user with RFID
        $user = User::where('rfid_uid', $rfid_uid)->first();

        if (!$user) {
            return back()->with('error', "User not found with RFID: $rfid_uid.");
        }

        // Check if user already has an active session
        $activeSession = Attendance::where('rfid_uid', $rfid_uid)
            ->whereNull('time_out')
            ->exists();

        if ($activeSession) {
            return back()->with('error', "User {$user->first_name} already has an active session. Please check out first.");
        }

        // Check if user already checked in today
        $todaySession = Attendance::where('rfid_uid', $rfid_uid)
            ->whereDate('time_in', Carbon::today())
            ->exists();

        if ($todaySession) {
            return back()->with('error', "User {$user->first_name} has already checked in today. Only one check-in per day is allowed.");
        }

        // Create new attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'rfid_uid' => $rfid_uid,
            'time_in' => Carbon::now(),
        ]);

        // Update user status
        $user->update([
            'session_status' => 'approved',
            'member_status' => 'active',
        ]);

        \Log::info("✅ User {$user->first_name} {$user->last_name} (RFID: {$user->rfid_uid}) Check-in recorded at " . now());

        return back()
            ->with('success', "✅ Check-in recorded successfully for {$user->first_name}.")
            ->with('checked_in', true);
    } catch (\Exception $e) {
        \Log::error("❌ Check-in error: " . $e->getMessage());
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}