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
        $today = $current_time->format('m-d');
        $identifier = $request->input('identifier');
        $action = $request->input('action');
    
        Log::info("Processing manual attendance for identifier: {$identifier}, action: {$action} at {$current_time}");
    
        try {
            // Find user by email or phone number
            $user = User::where('email', $identifier)
                        ->orWhere('phone_number', $identifier)
                        ->first();
    
            if (!$user) {
                Log::warning("No user found for identifier: {$identifier}");
                return redirect()->back()->with('error', 'User not found. Please check your email or phone number.');
            }
    
            // Verify the request is from the authenticated user
            if ($user->id !== Auth::id()) {
                Log::warning("Unauthorized attempt by user ID: " . Auth::id() . " for identifier: {$identifier}");
                return redirect()->back()->with('error', 'Unauthorized action.');
            }
    
            $full_name = $user->first_name;
            $rfid_uid = $user->rfid_uid;
            Log::info("User found: {$full_name} (ID: {$user->id}, RFID UID: {$rfid_uid})");
    
            // Check if today is the user's birthday
            $birthdate = $user->birthdate ? Carbon::parse($user->birthdate) : null;
            $is_birthday = $birthdate && $birthdate->format('m-d') === $today;
    
            // Check membership status
            if ($user->member_status === 'expired') {
                Log::warning("Membership expired for user ID: {$user->id}");
                return redirect()->back()->with('error', 'Membership expired! Attendance not recorded.');
            }
    
            if ($user->member_status === 'revoked') {
                Log::warning("Membership revoked for user ID: {$user->id}");
                return redirect()->back()->with('error', 'Membership revoked! Attendance not recorded.');
            }
    
            DB::beginTransaction();
    
            // Get today's attendance records
            $todays_attendance = DB::table('attendances')
                ->where('rfid_uid', $rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();
    
            if ($action === 'time_in') {
                if ($todays_attendance && !$todays_attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed in today");
                    DB::rollBack();
                    return redirect()->back()->with('error', 'You are already timed in.');
                }
    
                if ($todays_attendance && $todays_attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed out today");
                    DB::rollBack();
                    return redirect()->back()->with('error', 'You have already timed out today.');
                }
    
                // Record time-in
                DB::table('attendances')->insert([
                    'rfid_uid' => $rfid_uid,
                    'time_in' => $current_time,
                    'attendance_date' => $current_time->toDateString(),
                    'check_in_method' => 'manual',
                    'session_id' => $user->session_id ?? null,
                ]);
    
                DB::commit();
                Log::info("User {$full_name} (ID: {$user->id}) Time-in recorded at {$current_time}");
    
                $message = 'Time-in recorded successfully.';
                if ($is_birthday) {
                    $message = "Happy Birthday, {$full_name}! {$message}";
                }
                return redirect()->route('self.landingProfile')->with('success', $message);
            } else { // time_out
                if (!$todays_attendance) {
                    Log::info("User {$full_name} (ID: {$user->id}) has no time-in record today");
                    DB::rollBack();
                    return redirect()->back()->with('error', 'No time-in record found for today. Please time in first.');
                }
    
                if ($todays_attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed out today");
                    DB::rollBack();
                    return redirect()->back()->with('error', 'You have already timed out today.');
                }
    
                // Record time-out
                $updated = DB::table('attendances')
                    ->where('id', $todays_attendance->id)
                    ->update([
                        'time_out' => $current_time,
                        'check_out_method' => 'manual',
                    ]);
    
                if ($updated === 0) {
                    Log::warning("Failed to update time-out for attendance ID: {$todays_attendance->id}");
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Failed to record time-out. Please try again.');
                }
    
                DB::commit();
                Log::info("User {$full_name} (ID: {$user->id}) Time-out recorded at {$current_time}");
    
                $message = 'Time-out recorded successfully.';
                if ($is_birthday) {
                    $message = "Happy Birthday, {$full_name}! {$message}";
                }
                return redirect()->route('self.landingProfile')->with('success', $message);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error for manual attendance: " . json_encode($e->errors()));
            return redirect()->back()->with('error', $e->errors()['identifier'][0] ?? 'Invalid input.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing manual attendance for identifier {$identifier}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // ... other methods in SelfController ...
}