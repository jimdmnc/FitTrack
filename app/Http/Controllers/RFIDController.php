<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Session;

class RFIDController extends Controller
{

    // Get the latest UID (for AJAX polling)
    public function getLatestUid()
    {
        $latestUid = RfidTag::latest()->first();
        return response()->json([
            'uid' => $latestUid ? $latestUid->uid : null,
            'timestamp' => $latestUid ? $latestUid->created_at : null,

        ]);
    }

    public function saveUID(Request $request)
    {
        try {
            $validated = $request->validate([
                'uid' => 'required|string|max:30',
                'mode' => 'sometimes|string|in:registration,attendance'
            ]);

            $mode = $request->input('mode', 'registration');
            
            // Save the RFID tag
            $tag = RfidTag::create([
                'uid' => $validated['uid'],
            ]);
            
            // If in attendance mode, record the attendance
            if ($mode === 'attendance') {
                // Check if there's an existing "in" record without an "out" for this UID today
                $existingRecord = Attendance::where('rfid_uid', $validated['uid'])
                    ->whereDate('time_in', today())
                    ->whereNull('time_out')
                    ->first();
                
                if ($existingRecord) {
                    // User is checking out
                    $existingRecord->time_out = now();
                    $existingRecord->save();
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Checkout recorded successfully',
                        'attendance' => $existingRecord
                    ]);
                } else {
                    // User is checking in
                    $attendance = Attendance::create([
                        'rfid_uid' => $validated['uid'],
                        'time_in' => now(),
                    ]);
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Checkin recorded successfully',
                        'attendance' => $attendance
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'UID saved successfully',
                'data' => $tag
            ]);
        } catch (\Exception $e) {
            Log::error('Error in saveUID: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the latest RFID tag UID.
     *
     * @return \Illuminate\Http\Response
     */
}
