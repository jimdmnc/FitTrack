<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ✅ User Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Check if the user exists and validate password
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.']
            ]);
        }
    
        // Restrict login for admins
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admin accounts are not allowed on this app'], 403);
        }
    
        // Check if the user has an associated RFID UID
        if (!$user->rfid_uid) {
            return response()->json([
                'message' => 'No RFID UID associated with this user.',
            ], 400);
        }
    
        // Generate Token (ensure it is a plain text token)
        $token = $user->createToken('authToken')->plainTextToken;
    
        // Return response with user data and token
        return response()->json([
            'user' => $user,
            'token' => $token,
            'rfid_uid' => $user->rfid_uid,  // Include RFID UID
        ]);
    }

    // ✅ Get Authenticated User
// AuthController.php
public function user(Request $request)
{
    $user = $request->user(); // Get the authenticated user
    
    // Add null checks for dates using optional() helper
    return response()->json([
        'id' => $user->id,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        // 'full_name' => $user->first_name . ' ' . $user->last_name,
        'email' => $user->email,
        'membership_type' => $user->membership_type,
        'member_status' => $user->member_status,
        'start_date' => $user->start_date, // Will output "2025-04-07" (MySQL format)
        'end_date' => $user->end_date,
        'rfid_uid' => $user->rfid_uid,
        // Add any other fields you need from your users table
        'gender' => $user->gender,
        'phone_number' => $user->phone_number,
        'birthdate' => optional($user->birthdate)->format('M d, Y'),
        'session_status' => $user->session_status
    ]);
}


// public function getMemberByRfid($rfid_uid) 
// {
//     \Log::info("API Request for RFID: " . $rfid_uid);
    
//     $user = User::where('rfid_uid', $rfid_uid)->first();
    
//     if (!$user) {
//         \Log::warning("No member found for RFID: " . $rfid_uid);
//         return response()->json(['error' => 'Member not found'], 404);
//     }
    
//     return response()->json([
//         'id' => $user->id,
//         'first_name' => $user->first_name,
//         'last_name' => $user->last_name,
//         'full_name' => $user->first_name . ' ' . $user->last_name, // Changed from $member to $user
//         'membership_type' => $user->membership_type,
//         'member_status' => $user->member_status,
//         'start_date' => $user->start_date->format('M d, Y'),
//         'end_date' => $user->end_date ? $user->end_date->format('M d, Y') : null,
//         'rfid_uid' => $user->rfid_uid
//     ]);
// }

    // public function getDetailsByRfid($rfid)
    // {
    //     $details = UserDetails::where('rfid_uid', $rfid)->first();
    
    //     if (!$details) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User details not found'
    //         ], 404);
    //     }
    
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'age' => $details->age,
    //             'gender' => $details->gender,
    //             'weight' => $details->weight,
    //             'height' => $details->height,
    //             'activity_level' => $details->activity_level,
    //             'goal' => $details->goal,
    //             'target_muscle' => $details->target_muscle
    //         ]
    //     ]);
    // }

    
    // ✅ User Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Delete all tokens for the user
        return response()->json(['message' => 'Logged out successfully']);
    }
}

