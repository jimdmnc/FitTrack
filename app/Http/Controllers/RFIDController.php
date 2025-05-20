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
        // Validate input (accept JSON or form-urlencoded for backward compatibility)
        $uid = $request->input('uid');
        $rfid_uid = $request->input('rfid_uid', $uid); // Prefer rfid_uid from JSON, fallback to uid
        $time_in = $request->input('time_in') ? Carbon::parse($request->input('time_in'), 'Asia/Manila') : Carbon::now('Asia/Manila');
        $attendance_date = $request->input('attendance_date', $time_in->toDateString());
        $check_in_method = $request->input('check_in_method', 'rfid');

        if (!$rfid_uid) {
            return response()->json(['message' => 'RFID UID is required.'], 400);
        }

        DB::beginTransaction();

        try {
            // Check if user exists with the given RFID UID
            $user = DB::table('users')->where('rfid_uid', $rfid_uid)->first();

            if (!$user) {
                return response()->json(['message' => 'User not registered.', 'name' => ''], 404);
            }

            $full_name = $user->first_name . ' ' . $user->last_name;

            // Check member status
            if ($user->member_status === 'expired') {
                return response()->json(['message' => 'Membership expired! Attendance not recorded.', 'name' => $full_name], 403);
            }
            
            if ($user->member_status === 'revoked') {
                return response()->json(['message' => 'Membership revoked! Attendance not recorded.', 'name' => $full_name], 403);
            }

            // Check for duplicate attendance record
            $existingAttendance = DB::table('attendances')
                ->where('rfid_uid', $rfid_uid)
                ->where('attendance_date', $attendance_date)
                ->where('time_in', $time_in->toDateTimeString())
                ->first();

            if ($existingAttendance) {
                return response()->json(['message' => 'Attendance already recorded.', 'name' => $full_name], 400);
            }

            // Check if user has already checked in today without checking out
            $attendance = DB::table('attendances')
                ->where('rfid_uid', $rfid_uid)
                ->where('attendance_date', $attendance_date)
                ->whereNull('time_out')
                ->orderBy('time_in', 'desc')
                ->first();

            if ($attendance) {
                // User is checking out
                DB::table('attendances')->where('id', $attendance->id)->update([
                    'time_out' => $time_in,
                    'updated_at' => Carbon::now('Asia/Manila')
                ]);
                DB::commit();

                Log::info("User {$full_name} (UID: {$rfid_uid}) Time-out recorded at {$time_in}");
                return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name]);
            }

            // Insert new time-in record
            DB::table('attendances')->insert([
                'rfid_uid' => $rfid_uid,
                'time_in' => $time_in,
                'attendance_date' => $attendance_date,
                'check_in_method' => $check_in_method,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila')
            ]);
            DB::commit();

            Log::info("User {$full_name} (UID: {$rfid_uid}) Time-in recorded at {$time_in}");
            return response()->json(['message' => 'Time-in recorded successfully.', 'name' => $full_name]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Attendance error for UID {$rfid_uid}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Function to save RFID tag (unchanged)
    public function saveRFID(Request $request)
    {
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
                    'created_at' => $current_time
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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
        // Delete unregistered UIDs older than 2 minutes
        DB::table('rfid_tags')
            ->where('registered', 0)
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= 2')
            ->delete();
    }

    // Fetch the latest RFID UID from the rfid_tags table (unchanged)
    public function getLatestRFID()
    {
        $latestRFID = DB::table('rfid_tags')
            ->where('registered', 0)
            ->latest('created_at')
            ->first();
    
        if (!$latestRFID) {
            return response()->json(['error' => 'No registered RFID found.'], 404);
        }
    
        return response()->json(['uid' => $latestRFID->uid]);
    }

    // Clear RFID tag (unchanged)
    public function clear($uid)
    {
        $tag = RfidTag::where('uid', $uid)->first();

        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'RFID not found'], 404);
        }

        $tag->delete();
        return response()->json(['success' => true, 'message' => 'RFID cleared']);
    }
}