<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Session;

class RFIDController extends Controller
{

    // Fetch the latest RFID UID from the rfid_tags table
    public function getLatestRfidUid()
    {
        $rfidTag = RfidTag::where('registered', false)->latest()->first();
        
        if ($rfidTag) {
            return response()->json(['uid' => $rfidTag->uid]);
        } else {
            return response()->json(['uid' => null]);
        }
    }

 // Handle attendance logic
 public function handleAttendance(Request $request)
 {
     $uid = $request->input('uid'); // Get the UID from the request
     $current_time = Carbon::now();

     // Check if the user is registered
     $user = User::where('rfid_uid', $uid)->first();

     if ($user) {
         // Check if there's an existing attendance record for today
         $attendance = Attendance::where('rfid_uid', $uid)
             ->whereDate('time_in', $current_time->toDateString())
             ->first();

         if ($attendance) {
             // Update time_out if the user is tapping out
             $attendance->update(['time_out' => $current_time]);
         } else {
             // Create a new attendance record
             Attendance::create([
                 'rfid_uid' => $uid,
                 'time_in' => $current_time,
                 'time_out' => null,
             ]);
         }
     }

     return response()->json(['message' => 'Attendance handled successfully.']);
 }

    /**
     * Get the latest RFID tag UID.
     *
     * @return \Illuminate\Http\Response
     */
}
