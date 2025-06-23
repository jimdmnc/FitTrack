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

        Log::info("Processing manual attendance for identifier: {$identifier}, action: {$action} at {$current_time}");

        try {
            // Find user by email or phone number
            $user = User::where('email', $identifier)
                        ->orWhere('phone_number', $identifier)
                        ->first();

            if (!$user) {
                Log::warning("No user found for identifier: {$identifier}");
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. Please check your email or phone number.',
                ], 404);
            }

            // Verify the request is from the authenticated user
            if ($user->id !== Auth::id()) {
                Log::warning("Unauthorized attempt by user ID: " . Auth::id() . " for identifier: {$identifier}");
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.',
                ], 403);
            }

            $full_name = $user->first_name;
            Log::info("User found: {$full_name} (ID: {$user->id})");

            // Check membership status
            if ($user->member_status === 'expired') {
                Log::warning("Membership expired for user ID: {$user->id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Membership expired! Attendance not recorded.',
                ], 403);
            }

            if ($user->member_status === 'revoked') {
                Log::warning("Membership revoked for user ID: {$user->id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Membership revoked! Attendance not recorded.',
                ], 403);
            }

            DB::beginTransaction();

            $attendance = DB::table('attendances')
                ->where('rfid_uid', $user->rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            if ($action === 'time_in') {
                if ($attendance && !$attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed in today");
                    return response()->json([
                        'success' => false,
                        'message' => 'You are already timed in.',
                    ], 400);
                }

                if ($attendance && $attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) already timed out today");
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already timed out today.',
                    ], 400);
                }

                // Record time-in
                DB::table('attendances')->insert([
                    'rfid_uid' => $user->rfid_uid,
                    'time_in' => $current_time,
                    'attendance_date' => $current_time->toDateString(),
                    // 'manual_entry' => true, // Flag for manual entry
                ]);

                DB::commit();
                Log::info("User {$full_name} (ID: {$user->id}) Time-in recorded at {$current_time}");
                return response()->json([
                    'success' => true,
                    'message' => 'Time-in recorded successfully.',
                ]);
            } else { // time_out
                if (!$attendance || $attendance->time_out) {
                    Log::info("User {$full_name} (ID: {$user->id}) has no active time-in record");
                    return response()->json([
                        'success' => false,
                        'message' => 'No active time-in record found.',
                    ], 400);
                }

                // Record time-out
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update([
                        'time_out' => $current_time,
                        // 'manual_entry' => true, // Update flag
                    ]);

                DB::commit();
                Log::info("User {$full_name} (ID: {$user->id}) Time-out recorded at {$current_time}");
                return response()->json([
                    'success' => true,
                    'message' => 'Time-out recorded successfully.',
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error for manual attendance: " . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => $e->errors()['identifier'][0] ?? 'Invalid input.',
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing manual attendance for identifier {$identifier}: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

}