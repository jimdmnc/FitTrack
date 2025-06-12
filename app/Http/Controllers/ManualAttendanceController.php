<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class ManualAttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     * Apply authentication middleware to ensure only authenticated users can access.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the manual attendance page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch the latest attendance record for the authenticated user that hasn't been timed out
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereNull('time_out')
            ->latest()
            ->first();

        return view('attendance.manual', compact('attendance'));
    }

    /**
     * Handle manual time-in request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function timeIn(Request $request)
    {
        // Check if the user already has an active session (no time-out)
        $existingAttendance = Attendance::where('user_id', Auth::id())
            ->whereNull('time_out')
            ->first();

        if ($existingAttendance) {
            return redirect()->route('self.manualAttendance')
                ->with('error', 'You already have an active session. Please time out first.');
        }

        // Create a new attendance record
        Attendance::create([
            'user_id' => Auth::id(),
            'time_in' => Carbon::now(),
        ]);

        return redirect()->route('self.manualAttendance')
            ->with('success', 'Successfully timed in.');
    }

    /**
     * Handle manual time-out request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function timeOut(Request $request)
    {
        // Find the active attendance record for the user
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereNull('time_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return redirect()->route('self.manualAttendance')
                ->with('error', 'No active session found to time out.');
        }

        // Update the time-out field
        $attendance->update([
            'time_out' => Carbon::now(),
        ]);

        return redirect()->route('self.manualAttendance')
            ->with('success', 'Successfully timed out.');
    }
}