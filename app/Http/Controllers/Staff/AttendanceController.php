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
            case 'all':
                // No date filtering needed
                break;
            default:
                    // Default to today if invalid filter
                $filter = 'today';
                $query->whereDate('time_in', Carbon::today());
        }

        $attendances = $query->orderBy('time_in', 'desc')
            ->paginate(20)
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
            
            // if ($session->user) {
            //     $session->user->update([
            //         'session_status' => 'pending',
            //         'member_status' => 'expired',
            //     ]);
            // }
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
                
                // if ($session->user) {
                //     $session->user->update([
                //         'session_status' => 'pending',
                //         'member_status' => 'expired',
                //     ]);
                // }
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
            $request->validate([
                'uid'       => 'required|string',
                'timestamp' => 'required|date_format:Y-m-d H:i:s',
            ]);
    
            $uid       = $request->input('uid');
            $timestamp = $request->input('timestamp'); // ← FROM ESP32
    
            $user = User::where('rfid_uid', $uid)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not registered.',
                ], 404);
            }
    
            // Check active session
            $active = Attendance::where('rfid_uid', $uid)
                ->whereNull('time_out')
                ->exists();
    
            if ($active) {
                return response()->json([
                    'message' => 'Please wait until time-out is recorded.',
                    'name'    => $user->first_name . ' ' . $user->last_name,
                ], 400);
            }
    
            // Check if already checked in today (based on timestamp)
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->startOfDay();
            $todaySession = Attendance::where('rfid_uid', $uid)
                ->whereDate('time_in', $date)
                ->exists();
    
            if ($todaySession) {
                return response()->json([
                    'message' => 'Please wait until time-out is recorded.',
                    'name'    => $user->first_name . ' ' . $user->last_name,
                ], 400);
            }
    
            // USE ESP32 TIMESTAMP
            Attendance::create([
                'user_id'  => $user->id,
                'rfid_uid' => $uid,
                'time_in'  => $timestamp,
            ]);
    
            \Log::info("Time-in: {$user->first_name} at {$timestamp}");
    
            return response()->json([
                'message' => 'Time-in recorded successfully.',
                'name'    => $user->first_name . ' ' . $user->last_name,
            ]);
    
        } catch (\Exception $e) {
            \Log::error("Time-in error: " . $e->getMessage());
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}