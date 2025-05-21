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
        $uid = $request->input('uid');
        $current_time = Carbon\Carbon::now('Asia/Manila');
        $timestamp = $request->input('timestamp'); // Used for offline sync
        $has_timeout = $request->input('has_timeout', false);
        $timeout_timestamp = $request->input('timeout_timestamp'); // Used for offline sync timeout

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

            // Use provided timestamp for offline sync if available
            if ($timestamp) {
                $time_in = Carbon::createFromTimestamp($timestamp, 'Asia/Manila');
            } else {
                $time_in = $current_time;
            }

            // Check if user has already checked in today or if we have an explicit timeout request
            if ($has_timeout) {
                // Handle explicit timeout from offline sync
                $attendance = DB::table('attendances')
                    ->where('rfid_uid', $uid)
                    ->whereDate('time_in', Carbon::parse($time_in)->toDateString())
                    ->whereNull('time_out')
                    ->orderBy('time_in', 'desc')
                    ->first();
                
                if ($attendance) {
                    $time_out = $timeout_timestamp ? 
                        Carbon::createFromTimestamp($timeout_timestamp, 'Asia/Manila') : 
                        $current_time;
                    
                    DB::table('attendances')->where('id', $attendance->id)->update(['time_out' => $time_out]);
                    DB::commit();
                    
                    Log::info("User {$full_name} (UID: {$uid}) Time-out recorded at {$time_out} (offline sync)");
                    return response()->json([
                        'message' => 'Time-out recorded successfully.', 
                        'name' => $full_name
                    ]);
                } else {
                    // Create a new attendance record with both time-in and time-out
                    // This handles the case where the offline device has both records but server has neither
                    $time_out = $timeout_timestamp ? 
                        Carbon::createFromTimestamp($timeout_timestamp, 'Asia/Manila') : 
                        $current_time;
                    
                    $attendance_date = Carbon::parse($time_in)->toDateString();
                    
                    DB::table('attendances')->insert([
                        'rfid_uid' => $uid, 
                        'time_in' => $time_in,
                        'time_out' => $time_out,
                        'attendance_date' => $attendance_date,
                        'check_in_method' => 'rfid'
                    ]);
                    
                    DB::commit();
                    
                    Log::info("User {$full_name} (UID: {$uid}) Complete attendance record created (offline sync)");
                    return response()->json([
                        'message' => 'Time-out recorded successfully.', 
                        'name' => $full_name
                    ]);
                }
            } else {
                // Normal attendance flow (online or offline time-in)
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
                        return response()->json([
                            'message' => 'Time-out recorded successfully.', 
                            'name' => $full_name
                        ]);
                    }
                }

                // If no previous time-in today or already timed out, insert new time-in record
                $attendance_date = $timestamp ? 
                    Carbon::parse($time_in)->toDateString() : 
                    $current_time->toDateString();
                
                DB::table('attendances')->insert([
                    'rfid_uid' => $uid, 
                    'time_in' => $time_in,
                    'attendance_date' => $attendance_date,
                    'check_in_method' => 'rfid'
                ]);
                
                DB::commit();

                Log::info("User {$full_name} (UID: {$uid}) Time-in recorded at {$time_in}" . 
                    ($timestamp ? " (offline sync)" : ""));
                
                return response()->json([
                    'message' => 'Time-in recorded successfully.', 
                    'name' => $full_name
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Attendance error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Function to handle batch offline attendance synchronization
    public function syncOfflineAttendance(Request $request)
    {
        $records = $request->input('records', []);
        $results = [];
        
        Log::info("Received " . count($records) . " offline attendance records for sync");
        
        foreach ($records as $record) {
            try {
                $result = $this->processOfflineRecord($record);
                $results[] = [
                    'uid' => $record['uid'],
                    'status' => 'success',
                    'message' => $result['message']
                ];
            } catch (\Exception $e) {
                Log::error("Error processing offline record: " . $e->getMessage());
                $results[] = [
                    'uid' => $record['uid'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'message' => 'Batch processing completed',
            'results' => $results,
            'success_count' => collect($results)->where('status', 'success')->count(),
            'error_count' => collect($results)->where('status', 'error')->count()
        ]);
    }
    
    private function processOfflineRecord($record)
    {
        $uid = $record['uid'];
        $time_in = $record['time_in'];
        $time_out = $record['time_out'] ?? null;
        
        DB::beginTransaction();
        
        try {
            // Check if user exists with the given RFID UID
            $user = DB::table('users')->where('rfid_uid', $uid)->first();
            
            if (!$user) {
                return ['message' => 'User not registered.'];
            }
            
            $full_name = $user->first_name . ' ' . $user->last_name;
            
            // Check member status (we may want to still record expired/revoked users for offline sync)
            // but track their status for reporting
            $member_status = $user->member_status;
            
            // Convert timestamps to Carbon instances
            $time_in_carbon = Carbon::createFromTimestamp($time_in, 'Asia/Manila');
            $attendance_date = $time_in_carbon->toDateString();
            
            // Check if this record already exists to prevent duplicates
            $existingRecord = DB::table('attendances')
                ->where('rfid_uid', $uid)
                ->whereDate('attendance_date', $attendance_date)
                ->first();
            
            if ($existingRecord) {
                // If record exists but doesn't have time_out and we now have it
                if (is_null($existingRecord->time_out) && !is_null($time_out)) {
                    $time_out_carbon = Carbon::createFromTimestamp($time_out, 'Asia/Manila');
                    DB::table('attendances')
                        ->where('id', $existingRecord->id)
                        ->update(['time_out' => $time_out_carbon]);
                    
                    DB::commit();
                    
                    Log::info("Offline sync: Updated time-out for {$full_name} (UID: {$uid}) on {$attendance_date}");
                    return ['message' => 'Time-out updated successfully.'];
                } else {
                    // Record already exists fully
                    DB::commit();
                    return ['message' => 'Record already exists.'];
                }
            } else {
                // Create new attendance record
                $data = [
                    'rfid_uid' => $uid,
                    'time_in' => $time_in_carbon,
                    'attendance_date' => $attendance_date,
                    'check_in_method' => 'rfid_offline'
                ];
                
                // Add time_out if available
                if (!is_null($time_out)) {
                    $time_out_carbon = Carbon::createFromTimestamp($time_out, 'Asia/Manila');
                    $data['time_out'] = $time_out_carbon;
                }
                
                DB::table('attendances')->insert($data);
                DB::commit();
                
                Log::info("Offline sync: Created attendance record for {$full_name} (UID: {$uid}) on {$attendance_date}");
                return ['message' => 'Attendance record created successfully.'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
            ->where('registered', 0) // Fetch only temporary RFIDs
            ->latest('created_at') // Get the most recent entry
            ->first();
    
        if (!$latestRFID) {
            return response()->json(['error' => 'No pending RFID found.'], 404);
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