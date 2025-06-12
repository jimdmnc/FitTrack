<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\RfidTag;

class RFIDController extends Controller
{
    // Combined function to process RFID - handles both attendance and saving new RFID tags
    public function processRFID(Request $request)
    {
        $uid = $request->input('uid');
        $current_time = Carbon::now('Asia/Manila');
    
        Log::info("Processing RFID UID: {$uid} at {$current_time}");
    
        DB::beginTransaction();
    
        try {
            $user = DB::table('users')->where('rfid_uid', $uid)->first();
    
            if ($user) {
                $full_name = $user->first_name;
                Log::info("User found: {$full_name} (UID: {$uid})");
    
                if ($user->member_status === 'expired') {
                    Log::warning("Membership expired for UID: {$uid}");
                    return response()->json(['message' => 'Membership expired! Attendance not recorded.'], 403);
                }
    
                if ($user->member_status === 'revoked') {
                    Log::warning("Membership revoked for UID: {$uid}");
                    return response()->json(['message' => 'Membership revoked! Attendance not recorded.'], 403);
                }
    
                $attendance = DB::table('attendances')
                    ->where('rfid_uid', $uid)
                    ->whereDate('time_in', Carbon::today())
                    ->orderBy('time_in', 'desc')
                    ->first();
    
                if ($attendance) {
                    if (!$attendance->time_out) {
                        // Time-out case
                        DB::table('attendances')->where('id', $attendance->id)->update(['time_out' => $current_time]);
                        DB::commit();
                        Log::info("User {$full_name} (UID: {$uid}) Time-out recorded at {$current_time}");
                        return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name]);
                    } else {
                        // Already timed out today - prevent another time-in
                        DB::commit();
                        Log::info("User {$full_name} (UID: {$uid}) attempted to time-in after already timing out today");
                        return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name], 400);
                    }
                }
    
                // Regular time-in case (no attendance record for today)
                DB::table('attendances')->insert([
                    'rfid_uid' => $uid,
                    'time_in' => $current_time,
                    'attendance_date' => $current_time->toDateString()
                ]);
                DB::commit();
                Log::info("User {$full_name} (UID: {$uid}) Time-in recorded at {$current_time}");
                return response()->json(['message' => 'Time-in recorded successfully.', 'name' => $full_name]);
    
            } else {
                Log::info("No user found for UID: {$uid}, checking rfid_tags");
                $existingTag = DB::table('rfid_tags')->where('uid', $uid)->first();
    
                if (!$existingTag) {
                    DB::table('rfid_tags')->insert([
                        'uid' => $uid,
                        'registered' => 0,
                        'created_at' => $current_time
                    ]);
                    DB::commit();
                    Log::info("New RFID UID saved: {$uid}");
                    $this->cleanupUnregisteredUIDs();
                    return response()->json(['message' => 'RFID UID saved successfully. If not registered, it will be removed in 2 minutes.']);
                } else {
                    Log::info("UID {$uid} already exists in rfid_tags, registered: {$existingTag->registered}");
                    if ($existingTag->registered == 1) {
                        return response()->json(['message' => 'RFID UID is already registered.'], 400);
                    } else {
                        return response()->json(['message' => 'UID is pending registration.'], 400);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing UID {$uid}: {$e->getMessage()}");
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Helper function to clean up unregistered UIDs older than 2 minutes
    private function cleanupUnregisteredUIDs()
    {
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








    public function manualTimeIn(Request $request)
    {
        $user = Auth::user();
        $current_time = Carbon::now('Asia/Manila');
        $uid = $user->rfid_uid;

        Log::info("Processing manual time-in for user: {$user->first_name} (UID: {$uid}) at {$current_time}");

        DB::beginTransaction();

        try {
            if ($user->member_status === 'expired') {
                Log::warning("Membership expired for UID: {$uid}");
                return redirect()->back()->with('error', 'Membership expired! Attendance not recorded.');
            }

            if ($user->member_status === 'revoked') {
                Log::warning("Membership revoked for UID: {$uid}");
                return redirect()->back()->with('error', 'Membership revoked! Attendance not recorded.');
            }

            $attendance = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('time_in', Carbon::today())
                ->orderBy('time_in', 'desc')
                ->first();

            if ($attendance && !$attendance->time_out) {
                Log::info("User {$user->first_name} (UID: {$uid}) already timed in today");
                return redirect()->back()->with('error', 'You have already timed in today.');
            }

            if ($attendance && $attendance->time_out) {
                Log::info("User {$user->first_name} (UID: {$uid}) already timed out today");
                return redirect()->back()->with('error', 'You have already timed out today.');
            }

            DB::table('attendances')->insert([
                'rfid_uid' => $uid,
                'time_in' => $current_time,
                'attendance_date' => $current_time->toDateString()
            ]);
            DB::commit();
            Log::info("User {$user->first_name} (UID: {$uid}) Manual time-in recorded at {$current_time}");
            return redirect()->back()->with('success', 'Time-in recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing manual time-in for UID {$uid}: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while recording time-in.');
        }
    }



}