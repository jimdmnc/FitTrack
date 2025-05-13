<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
    // ✅ User Login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => ['email' => ['Invalid credentials']]
                ], 401); // HTTP 401 for unauthorized
            }
    
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin accounts are not allowed on this app'
                ], 403);
            }
            
            if ($user->role === 'userSession') {
                return response()->json([
                    'success' => false,
                    'message' => 'Not Regular members are not allowed on this app'
                ], 403);
            }
    
            if (!$user->rfid_uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'No RFID UID associated with this user'
                ], 400);
            }
    
            $token = $user->createToken('authToken')->plainTextToken;
    
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token,
                'rfid_uid' => $user->rfid_uid
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Get Authenticated User
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




    
    // ✅ User Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Delete all tokens for the user
        return response()->json(['message' => 'Logged out successfully']);
    }



    public function changePassword(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string|same:new_password',
            ]);
    
            // Get the authenticated user
            $user = $request->user();
            
            // Check if user has a password set (since password field is nullable)
            if (empty($user->password)) {
                return response()->json([
                    'message' => 'No password set for this account',
                    'errors' => [
                        'current_password' => ['This account has no password set']
                    ]
                ], 422);
            }
    
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided password does not match our records']
                ]);
            }
    
            // Update password and clear remember token (security best practice)
            $user->update([
                'password' => Hash::make($validated['new_password']),
                'remember_token' => null,
            ]);
    
            // Revoke all other tokens (security measure)
            $user->tokens()->delete();
    
            return response()->json([
                'message' => 'Password changed successfully',
                'status' => 'success',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'updated_at' => $user->updated_at
                ]
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'status' => 'error'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password change failed',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone_number' => 'nullable|string|max:11',
            'birthdate' => 'nullable|date',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $user->update($validator->validated());
            
            // Refresh the user model to get updated data
            $user->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->only([
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'phone_number',
                        'birthdate',
                        'membership_type',
                        'start_date',
                        'end_date',
                        'rfid_uid',
                        'member_status'
                    ])
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

