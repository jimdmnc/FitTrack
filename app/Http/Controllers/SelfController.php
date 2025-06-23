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
        $today = $current_time->format('m-d'); // For birthday check
        $identifier = $request->input('identifier');
        $action = $request->input('action');
        $debug_action = $request->input('debug_action', 'unknown');

        Log::info("Processing manual attendance for identifier: {$identifier}, action: {$action}, debug_action: {$debug_action} at {$current_time}");

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

            // Check for double-tap prevention (10-second rule)
            $latest_attendance = DB::table('attendances')
                ->where('rfid_uid', $rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            if ($latest_attendance) {
                $last_action_time = $latest_attendance->time_out ?? $latest_attendance->time_in;
                $time_diff = $current_time->diffInSeconds(Carbon::parse($last_action_time));
                if ($time_diff < 10) {
                    Log::warning("Double-tap attempt by user ID: {$user->id} within {$time_diff} seconds");
                    return redirect()->back()->with('error', "Please wait " . (10 - $time_diff) . " seconds before recording again.");
                }
            }

            $attendance = $latest_attendance;

            if ($action === 'time_in') {
                if ($attendance && !$attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed in today");
                    return redirect()->back()->with('error', 'You are already timed in.');
                }

                if ($attendance && $attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed out today");
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
                if (!$attendance || $attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) has no active time-in record");
                    return redirect()->back()->with('error', 'No active time-in record found. Please time in first.');
                }

                // Record time-out
                $updated = DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->whereNull('time_out')
                    ->insert([
                        'time_out' => $current_time,
                        'check_in_method' => 'manual',
                    ]);

                if ($updated === 0) {
                    Log::warning("Failed to update time-out for attendance ID: {$attendance->id}");
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