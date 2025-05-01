<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


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



     /**
     * Update user profile by RFID UID
     */
    public function updateProfileByRfid(Request $request, $rfid_uid)
    {
        try {
            $user = User::where('rfid_uid', $rfid_uid)->firstOrFail();
            
            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255|unique:users,email,'.$user->id,
                'phone_number' => 'sometimes|required|string|max:20',
                'birthdate' => 'sometimes|required|date',
                'gender' => 'sometimes|required|in:male,female,other',
                // Add validation for other fields as needed
            ]);

            // Handle email change verification
            if ($request->has('email') && $request->email !== $user->email) {
                $validated['email_verified_at'] = null;
            }

            $user->update($validated);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $this->formatUserResponse($user)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Change password by RFID UID
     */
    public function changePasswordByRfid(Request $request, $rfid_uid)
    {
        try {
            $user = User::where('rfid_uid', $rfid_uid)->firstOrFail();
            
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|same:new_password',
            ]);

            // Check if user has a password set
            if (empty($user->password)) {
                return response()->json([
                    'message' => 'No password set for this account',
                    'errors' => ['current_password' => ['This account has no password set']]
                ], 422);
            }

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided password does not match our records']
                ]);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password']),
                'remember_token' => null,
            ]);

            // Revoke all tokens
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Password changed successfully',
                'status' => 'success'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Send verification email by RFID UID
     */
    public function sendVerificationEmailByRfid(Request $request, $rfid_uid)
    {
        try {
            $user = User::where('rfid_uid', $rfid_uid)->firstOrFail();
            
            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified']);
            }

            $user->sendEmailVerificationNotification();

            return response()->json(['message' => 'Verification email sent']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    /**
     * Format user response consistently
     */
    private function formatUserResponse(User $user)
    {
        return [
            'id' => $user->id,
            'rfid_uid' => $user->rfid_uid,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'birthdate' => $user->birthdate ? Carbon::parse($user->birthdate)->format('Y-m-d') : null,
            'membership_type' => $user->membership_type,
            'member_status' => $user->member_status,
            'session_status' => $user->session_status,
            'start_date' => $user->start_date,
            'end_date' => $user->end_date,
            'email_verified' => $user->hasVerifiedEmail(),
            'needs_approval' => (bool)$user->needs_approval,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];
    }
}

