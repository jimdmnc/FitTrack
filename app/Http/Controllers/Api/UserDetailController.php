<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDetailController extends Controller
{
    // Store user details
    public function store(Request $request)
    {
        $request->validate([
            'age' => 'required|integer',
            'gender' => 'required|in:Male,Female,Other',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'activity_level' => 'required|in:Beginner,Intermediate,Advanced',
        ]);

        $user = Auth::user(); // Get authenticated user

        // Ensure the user has an RFID UID
        if (!$user->rfid_uid) {
            return response()->json(['message' => 'RFID UID not found for the user'], 400);
        }

        // Save or update details
        $userDetail = UserDetail::updateOrCreate(
            ['rfid_uid' => $user->rfid_uid],  // Match RFID UID instead of user_id
            $request->all()
        );

        return response()->json([
            'message' => 'User details saved successfully',
            'data' => $userDetail
        ]);
    }

    // Get user details
    public function show()
    {
        $user = Auth::user();
        
        if (!$user->rfid_uid) {
            return response()->json(['message' => 'RFID UID not found for the user'], 400);
        }

        $userDetail = UserDetail::where('rfid_uid', $user->rfid_uid)->first();

        if (!$userDetail) {
            return response()->json(['message' => 'No details found'], 404);
        }

        return response()->json(['data' => $userDetail]);
    }
}
