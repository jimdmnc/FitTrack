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
  public function processRFID(Request $request)
  {
      // Validate UID input
      $uid = $request->input('uid');
      if (!$uid || !preg_match('/^[0-9A-Fa-f]{8,}$/', $uid)) {
          Log::warning("Invalid UID format received: {$uid}");
          return response()->json(['message' => 'Invalid UID format.'], 400);
      }
      $uid = strtolower($uid); // Normalize to uppercase

      // Rate-limiting: Prevent excessive requests for the same UID
      $cacheKey = 'rfid_scan_' . $uid;
      if (Cache::has($cacheKey)) {
          Log::warning("Rate limit exceeded for UID: {$uid}");
          return response()->json(['message' => 'Please wait a few seconds before scanning again.'], 429);
      }
      Cache::put($cacheKey, true, now()->addSeconds(5)); // 5-second lock

      $current_time = Carbon::now('Asia/Manila');

      DB::beginTransaction();

      try {
          // Check if UID exists in rfid_tags
          $rfidTag = DB::table('rfid_tags')->where('uid', $uid)->first();

          // If UID not in rfid_tags, add as unregistered
          if (!$rfidTag) {
              DB::table('rfid_tags')->insert([
                  'uid' => $uid,
                  'registered' => 0,
                  'created_at' => $current_time,
              ]);
              Log::info("Unregistered UID saved: {$uid}");
          } elseif ($rfidTag->registered == 0) {
              Log::info("UID pending registration: {$uid}");
              DB::commit();
              return response()->json(['message' => 'UID is pending registration.', 'name' => ''], 400);
          }

          // Check if user exists with the given RFID UID
          $user = DB::table('users')->where('rfid_uid', $uid)->first();

          if (!$user) {
              Log::warning("User not found for UID: {$uid}");
              DB::commit();
              return response()->json(['message' => 'User not registered.'], 404);
          }

          $full_name = trim($user->first_name . ' ' . $user->last_name);

          // Check member status
          if ($user->member_status === 'expired') {
              Log::warning("Membership expired for UID: {$uid}");
              DB::commit();
              return response()->json(['message' => 'Membership expired! Attendance not recorded.', 'name' => $full_name], 403);
          }

          if ($user->member_status === 'revoked') {
              Log::warning("Membership revoked for UID: {$uid}");
              DB::commit();
              return response()->json(['message' => 'Membership revoked! Attendance not recorded.', 'name' => $full_name], 403);
          }

          // Check for recent attendance (within last 24 hours)
          $attendance = DB::table('attendances')
              ->where('rfid_uid', $uid)
              ->where('time_in', '>=', $current_time->copy()->subHours(24))
              ->whereNull('time_out')
              ->orderBy('time_in', 'desc')
              ->first();

          if ($attendance) {
              // Prevent duplicate time-in within 5 minutes
              $lastTimeIn = Carbon::parse($attendance->time_in);
              if ($current_time->diffInSeconds($lastTimeIn) < 300) {
                  Log::info("Duplicate time-in attempt for UID: {$uid}");
                  DB::commit();
                  return response()->json(['message' => 'Please wait before scanning again.', 'name' => $full_name], 429);
              }

              // Record time-out
              DB::table('attendances')->where('id', $attendance->id)->update(['time_out' => $current_time]);
              DB::commit();
              Log::info("User {$full_name} (UID: {$uid}) Time-out recorded at {$current_time}");
              return response()->json(['message' => 'Time-out recorded successfully.', 'name' => $full_name]);
          }

          // Record new time-in
          DB::table('attendances')->insert([
              'rfid_uid' => $uid,
              'time_in' => $current_time,
              'attendance_date' => $current_time->toDateString(),
          ]);
          DB::commit();
          Log::info("User {$full_name} (UID: {$uid}) Time-in recorded at {$current_time}");
          return response()->json(['message' => 'Time-in recorded successfully.', 'name' => $full_name]);
      } catch (\Exception $e) {
          DB::rollBack();
          Log::error("Error processing RFID UID {$uid}: " . $e->getMessage());
          return response()->json(['message' => 'Server error occurred.', 'error' => $e->getMessage()], 500);
      }
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
