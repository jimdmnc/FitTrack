<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Session;

class RFIDController extends Controller
{
    // // Store the latest UID temporarily (before saving)
    // public function storeUID(Request $request)
    // {
    //     $request->validate([
    //         'rfid_uid' => 'required|string',
    //     ]);

    //     // Store the latest UID in the session
    //     Session::put('latest_rfid', $request->rfid_uid);

    //     return response()->json(['message' => 'RFID received', 'rfid_uid' => $request->rfid_uid]);
    // }

    // Get the latest UID (for AJAX polling)
    public function getLatestUid()
    {
        $latestUid = RfidTag::latest()->first();
        return response()->json([
            'uid' => $latestUid ? $latestUid->uid : null,
            'timestamp' => $latestUid ? $latestUid->created_at : null,

        ]);
    }

    // Save UID to database
    public function saveUID(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string|unique:rfids',
        ]);

        RFID::create(['rfid_uid' => $request->rfid_uid]);

        return redirect()->back()->with('success', 'RFID saved successfully!');
    }
}
