<?php


namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = Attendance::where('user_id', $user->id)
            ->whereNotNull('time_in')
            ->get(['time_in', 'time_out'])
            ->map(function ($attendance) {
                $date = Carbon::parse($attendance->time_in);
                $duration = $attendance->time_out ? $date->diffInMinutes(Carbon::parse($attendance->time_out)) . ' minutes' : 'N/A';
                return [
                    'time_in' => $date->toDateTimeString(),
                    'time_out' => $attendance->time_out ? Carbon::parse($attendance->time_out)->toDateTimeString() : null,
                    'formatted_duration' => $duration,
                ];
            });

        $userData = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'membership_type' => $user->membership_type ?? 'N/A',
            'attendances' => $attendances->toArray(),
        ];

        Log::info('AttendanceController: Track attendance accessed', [
            'user_id' => $user->id,
            'attendance_count' => $attendances->count(),
        ]);

        return view('trackAttendance', [
            'selectedAttendance' => ['user' => $userData],
        ]);
    }
}