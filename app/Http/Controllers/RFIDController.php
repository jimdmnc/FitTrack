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
  // Function to handle attendance (time-in / time-out)
public function handleAttendance(Request $request)
{
    $uid = $request->input('uid');
    $current_time = Carbon::now('Asia/Manila');

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

        // Check if user has already checked in today
        $attendance = DB::table('attendances')
            ->where('rfid_uid', $uid)
            ->whereDate('time_in', Carbon::today())
            ->orderBy('time_in', 'desc')
            ->first();

        if ($attendance) {
            if (!$attendance->time_out) {
                // User is checking out (if time_out is null)
                DB::table('attendances')->where('id', $attendance->id)->update(['time_out' => $current_time]);
                DB::commit();

                Log::info("User {$full_name} (UID: {$uid}) Time-out recorded at {$current_time}");
                return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name]);
            }
        }

        // If no previous time-in today or already timed out, insert new time-in record
        DB::table('attendances')->insert([
            'rfid_uid' => $uid, 
            'time_in' => $current_time,
            'attendance_date' => $current_time->toDateString()
        ]);
        DB::commit();

        Log::info("User {$full_name} (UID: {$uid}) Time-in recorded at {$current_time}");
        return response()->json(['message' => 'Time-in recorded successfully.', 'name' => $full_name]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    // Function to save RFID tag
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

    // Fetch the latest RFID UID from the rfid_tags table
    public function getLatestRFID()
    {
        $latestRFID = DB::table('rfid_tags')
            ->where('registered', 0) // Fetch only registered RFIDs
            ->latest('created_at') // Get the most recent entry
            ->first();
    
        if (!$latestRFID) {
            return response()->json(['error' => 'No registered RFID found.'], 404);
        }
    
        return response()->json(['uid' => $latestRFID->uid]);
    }

    
    public function clear($uid)
    {
        $tag = RfidTag::where('uid', $uid)->first();

        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'RFID not found'], 404);
        }

        $tag->delete(); // Or you can update registered to 0: $tag->update(['registered' => 0]);

        return response()->json(['success' => true, 'message' => 'RFID cleared']);
    }
}
