<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDetail; // Updated to use UserDetail model
use Illuminate\Support\Facades\Auth;

class WalkthroughController extends Controller
{
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'gender' => 'required|string|max:10',
            'activity_level' => 'required|string|max:50',
            'age' => 'required|integer|min:1',
            'height' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'target_muscle' => 'required|string|max:50',
            'goal' => 'required|string|max:255',
        ]);

        // ✅ Authenticate user
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // ✅ Ensure user has an RFID UID
        if (!$user->rfid_uid) {
            return response()->json(['message' => 'RFID UID not found'], 404);
        }

        // ✅ Insert into `user_details` table
        try {
            $data = UserDetail::create([ // Using UserDetail model
                'rfid_uid' => $user->rfid_uid,
                'gender' => $request->input('gender'),
                'activity_level' => $request->input('activity_level'),
                'age' => $request->input('age'),
                'height' => $request->input('height'),
                'weight' => $request->input('weight'),
                'target_muscle' => $request->input('target_muscle'),
                'goal' => $request->input('goal'),
            ]);

            return response()->json(['message' => 'Data saved successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error saving data', 'error' => $e->getMessage()], 500);
        }
    }
}
