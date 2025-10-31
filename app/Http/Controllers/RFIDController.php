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
        $request->validate([
            'uid'       => 'required|string',
            'timestamp' => 'required|date_format:Y-m-d H:i:s', // ← FROM ESP32
        ]);
    
        $uid       = $request->input('uid');
        $timestamp = $request->input('timestamp'); // ← USE THIS
    
        Log::info("Processing RFID UID: {$uid} at {$timestamp}");
    
        DB::beginTransaction();
    
        try {
            $user = DB::table('users')->where('rfid_uid', $uid)->first();
    
            if ($user) {
                $full_name = $user->first_name . ' ' . $user->last_name;
                Log::info("User found: {$full_name} (UID: {$uid})");
    
                // --- Membership Checks ---
                if ($user->member_status === 'expired') {
                    DB::commit();
                    return response()->json([
                        'message' => 'Membership expired! Attendance not recorded.',
                        'name'    => $full_name
                    ], 403);
                }
    
                if ($user->member_status === 'revoked') {
                    DB::commit();
                    return response()->json([
                        'message' => 'Membership revoked! Attendance not recorded.',
                        'name'    => $full_name
                    ], 403);
                }
    
                // --- Get today's date from ESP32 timestamp ---
                $tapDate = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp)->startOfDay();
    
                $attendance = DB::table('attendances')
                    ->where('rfid_uid', $uid)
                    ->whereDate('time_in', $tapDate)
                    ->orderBy('time_in', 'desc')
                    ->first();
    
                if ($attendance) {
                    if (!$attendance->time_out) {
                        // TIME-OUT
                        DB::table('attendances')
                            ->where('id', $attendance->id)
                            ->update(['time_out' => $timestamp]);
    
                        DB::commit();
                        Log::info("Time-out: {$full_name} at {$timestamp}");
                        return response()->json([
                            'message' => 'Time-out recorded successfully.',
                            'name'    => $full_name
                        ]);
                    } else {
                        DB::commit();
                        return response()->json([
                            'message' => 'Please wait until time-out is recorded.',
                            'name'    => $full_name
                        ], 400);
                    }
                }
    
                // TIME-IN (no record today)
                DB::table('attendances')->insert([
                    'rfid_uid'        => $uid,
                    'time_in'         => $timestamp,
                    'attendance_date' => $tapDate->toDateString(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
    
                DB::commit();
                Log::info("Time-in: {$full_name} at {$timestamp}");
                return response()->json([
                    'message' => 'Time-in recorded successfully.',
                    'name'    => $full_name
                ]);
    
            } else {
                // --- Unknown UID → Save to rfid_tags ---
                Log::info("No user for UID: {$uid}, saving to rfid_tags");
    
                $existingTag = DB::table('rfid_tags')->where('uid', $uid)->first();
    
                if (!$existingTag) {
                    DB::table('rfid_tags')->insert([
                        'uid'        => $uid,
                        'registered' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::commit();
                    $this->cleanupUnregisteredUIDs();
                    return response()->json([
                        'message' => 'RFID UID saved. Register within 2 minutes.'
                    ]);
                } else {
                    if ($existingTag->registered == 1) {
                        return response()->json(['message' => 'RFID already registered.'], 400);
                    } else {
                        return response()->json(['message' => 'UID pending registration.'], 400);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("RFID Error: " . $e->getMessage());
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


    
}