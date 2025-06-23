<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;

class SelfController extends Controller
{
    public function showForgotRfid()
    {
        return view('self.forgot-rfid');
    }

    public function manualAttendance(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'action' => 'required|in:time_in,time_out',
        ]);
    
        $current_time = Carbon::now('Asia/Manila');
        $identifier = $request->input('identifier');
        $action = $request->input('action');
    
        try {
            DB::beginTransaction();
    
            // Find user
            $user = User::where('email', $identifier)
                      ->orWhere('phone_number', $identifier)
                      ->firstOrFail();
    
            // Verify authentication
            if ($user->id !== Auth::id()) {
                throw new \Exception('Unauthorized action');
            }
    
            // Check membership status
            if ($user->member_status === 'expired') {
                throw new \Exception('Membership expired! Attendance not recorded.');
            }
            if ($user->member_status === 'revoked') {
                throw new \Exception('Membership revoked! Attendance not recorded.');
            }
    
            $todays_attendance = DB::table('attendances')
                ->where('rfid_uid', $user->rfid_uid)
                ->whereDate('time_in', $current_time->toDateString())
                ->latest('time_in')
                ->first();
    
            if ($action === 'time_in') {
                if ($todays_attendance && !$todays_attendance->time_out) {
                    throw new \Exception('You are already timed in.');
                }
    
                DB::table('attendances')->insert([
                    'rfid_uid' => $user->rfid_uid,
                    'time_in' => $current_time,
                    'attendance_date' => $current_time->toDateString(),
                    'check_in_method' => 'manual',
                    'session_id' => $user->session_id,
                ]);
    
                $message = 'Time-in recorded successfully.';
            } else { // time_out
                if (!$todays_attendance) {
                    throw new \Exception('No time-in record found for today.');
                }
                if ($todays_attendance->time_out) {
                    throw new \Exception('You have already timed out today.');
                }
    
                DB::table('attendances')
                    ->where('id', $todays_attendance->id)
                    ->update([
                        'time_out' => $current_time,
                        'check_out_method' => 'manual',
                    ]);
    
                $message = 'Time-out recorded successfully.';
            }
    
            DB::commit();
            return redirect()->route('self.landingProfile')->with('success', $message);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Attendance Error: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ... other methods in SelfController ...
}