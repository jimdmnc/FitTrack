<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
    public function user(Request $request)
    {
        return response()->json($request->user()); // Return the authenticated user
    }

    // ✅ User Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Delete all tokens for the user
        return response()->json(['message' => 'Logged out successfully']);
    }
}

