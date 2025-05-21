<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\RfidTag;

class RFIDController extends Controller
{
    // Function to handle attendance (time-in / time-out)
    public function handleAttendance(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'uid' => 'required|string|max:50',
            'time_in' => 'nullable|date_format:Y-m-d H:i:s',
            'attendance_date' => 'nullable|date_format:Y-m-d',
            'check_in_method' => 'nullable|string|max:255',
        ]);

        $uid = $request->input('uid');
        $time_in = $request->input('time_in', Carbon::now('Asia/Manila')->format('Y-m-d H:i:s'));
        $attendance_date = $request->input('attendance_date', Carbon::today('Asia/Manila')->toDateString());
        $check_in_method = $request->input('check_in_method', 'rfid');

        DB::beginTransaction();

        try {
            // Check if user exists with the given RFID UID
            $user = DB::table('users')->where('rfid_uid', $uid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not registered.'], 404);
            }

            $full_name = $user->first_name . ' ' . $user->last_name;

            // Check member status
            if ($user->member_status === 'expired') {
                return response()->json(['message' => 'Membership expired! Attendance not recorded.'], 403);
            }
            
            if ($user->member_status === 'revoked') {
                return response()->json(['message' => 'Membership revoked! Attendance not recorded.'], 403);
            }

            // Check if user has already checked in on the given date
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('attendance_date', $attendance_date)
                ->orderBy('time_in', 'desc')
                ->first();

            if ($attendance && !$attendance->time_out) {
                // User is checking out (if time_out is null)
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update([
                        'time_out' => $time_in,
                        'updated_at' => Carbon::now('Asia/Manila'),
                    ]);
                DB::commit();

                Log::info("User {$full_name} (UID: {$uid}) Time-out recorded at {$time_in}");
                return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name]);
            }

            // If no previous time-in on the date or already timed out, insert new time-in record
            DB::table('attendances')->insert([
                'rfid_uid' => $uid,
                'time_in' => $time_in,
                'attendance_date' => $attendance_date,
                'check_in_method' => $check_in_method,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila'),
            ]);
            DB::commit();

            Log::info("User {$full_name} (UID: {$uid}) Time-in recorded at {$time_in}");
            return response()->json(['message' => 'Time-in recorded successfully.', 'name' => $full_name]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error handling attendance for UID {$uid}: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to process attendance: ' . $e->getMessage()], 500);
        }
    }

    // Function to save RFID tag
    public function saveRFID(Request $request)
    {
        $request->validate([
            'uid' => 'required|string|max:50',
        ]);

        $uid = $request->input('uid');
        $current_time = Carbon::now('Asia/Manila');

        DB::beginTransaction();

        try {
            // Check if the RFID UID already exists in the rfid_tags table
            $existingTag = DB::table('rfid_tags')->where('uid', $uid)->first();

            if (!$existingTag) {
                // Insert the new UID into rfid_tags with registered = 0 (temporary)
                DB::table('rfid_tags')->insert([
                    'uid' => $uid,
                    'registered' => 0,
                    'created_at' => $current_time,
                    'updated_at' => $current_time,
                ]);

                DB::commit();
                return response()->json(['message' => 'RFID UID saved successfully. If not registered, it will be removed in 2 minutes.']);
            } else {
                if ($existingTag->registered == 1) {
                    return response()->json(['message' => 'RFID UID is already registered.'], 400);
                } else {
                    return response()->json(['message' => 'UID is pending registration.'], 400);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving RFID UID {$uid}: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to save RFID: ' . $e->getMessage()], 500);
        }
    }

    // Function to clean up unregistered UIDs
    public function cleanupRFIDTags()
    {
        try {
            DB::table('rfid_tags')
                ->where('registered', 0)
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= 2')
                ->delete();
            return response()->json(['message' => 'Unregistered RFID tags cleaned up.']);
        } catch (\Exception $e) {
            Log::error("Error cleaning up RFID tags: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to clean up RFID tags: ' . $e->getMessage()], 500);
        }
    }

    // Fetch the latest RFID UID from the rfid_tags table
    public function getLatestRFID()
    {
        try {
            $latestRFID = DB::table('rfid_tags')
                ->where('registered', 0)
                ->latest('created_at')
                ->first();

            if (!$latestRFID) {
                return response()->json(['error' => 'No unregistered RFID found.'], 404);
            }

            return response()->json(['uid' => $latestRFID->uid]);
        } catch (\Exception $e) {
            Log::error("Error fetching latest RFID: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to fetch RFID: ' . $e->getMessage()], 500);
        }
    }

    // Clear a specific RFID tag
    public function clear($uid)
    {
        try {
            $tag = RfidTag::where('uid', $uid)->first();

            if (!$tag) {
                return response()->json(['success' => false, 'message' => 'RFID not found'], 404);
            }

            $tag->delete();
            return response()->json(['success' => true, 'message' => 'RFID cleared']);
        } catch (\Exception $e) {
            Log::error("Error clearing RFID UID {$uid}: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to clear RFID: ' . $e->getMessage()], 500);
        }
    }
}