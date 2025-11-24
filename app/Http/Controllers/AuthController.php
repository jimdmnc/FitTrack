<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // User Login
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
                ], 401);
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
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadProfileImage(Request $request)
    {
        try {
            Log::info('Upload profile image request received', [
                'rfid_uid' => $request->rfid_uid,
                'has_file' => $request->hasFile('profile_image')
            ]);
    
            $validator = Validator::make($request->all(), [
                'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'rfid_uid' => 'required|string|exists:users,rfid_uid',
            ]);
    
            if ($validator->fails()) {
                Log::warning('Validation failed: ' . $validator->errors()->first());
                return response()->json(['error' => $validator->errors()->first()], 422);
            }
    
            $user = User::where('rfid_uid', $request->rfid_uid)->first();
            if (!$user) {
                Log::error('User not found for rfid_uid: ' . $request->rfid_uid);
                return response()->json(['error' => 'User not found'], 404);
            }
    
            // Get the uploaded file
            $file = $request->file('profile_image');
            
            // Generate unique filename
            $filename = $user->rfid_uid . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Create profiles directory if it doesn't exist
            $uploadPath = public_path('profiles');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
    
            // Move the file to public/profiles
            $file->move($uploadPath, $filename);
            
            // Save relative path (without 'public/' prefix)
            $relativePath = 'profiles/' . $filename;
            $user->profile_image = $relativePath;
            $user->save();
    
            // Generate full URL
            $fullUrl = url('/' . $relativePath);
    
            Log::info('Profile image uploaded successfully', [
                'rfid_uid' => $request->rfid_uid,
                'path' => $relativePath,
                'full_url' => $fullUrl
            ]);
    
            return response()->json([
                'message' => 'Profile image uploaded successfully',
                'path' => $relativePath,
                'url' => $fullUrl
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error uploading profile image: ' . $e->getMessage(), [
                'rfid_uid' => $request->rfid_uid,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to upload image: ' . $e->getMessage()], 500);
        }
    }
    // Get Authenticated User
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'membership_type' => $user->membership_type,
            'member_status' => $user->member_status,
            'start_date' => $user->start_date,
            'end_date' => $user->end_date,
            'rfid_uid' => $user->rfid_uid,
            'gender' => $user->gender,
            'phone_number' => $user->phone_number,
            'birthdate' => $user->birthdate, // Just return the raw value
            'session_status' => $user->session_status,
            'profile_image' => $user->profile_image ? Storage::url($user->profile_image) : null
        ]);
    }

    // User Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string|same:new_password',
            ]);
    
            $user = $request->user();
            
            if (empty($user->password)) {
                return response()->json([
                    'message' => 'No password set for this account',
                    'errors' => [
                        'current_password' => ['This account has no password set']
                    ]
                ], 422);
            }
    
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['The provided password does not match our records']
                ]);
            }
    
            $user->update([
                'password' => Hash::make($validated['new_password']),
                'remember_token' => null,
            ]);
    
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

    public function saveToken(Request $request)
    {
        try {
            // Validate the incoming token
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                Log::warning('Validation failed for saveToken: ' . $validator->errors()->first());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Get the authenticated user
            $user = $request->user();
    
            if (!$user) {
                Log::error('User not authenticated in saveToken');
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
    
            // Update the user's fcm_token
            $token = $request->input('token');
            $user->update(['fcm_token' => $token]);
    
            Log::info('FCM token saved successfully', ['user_id' => $user->id, 'fcm_token' => $token]);
    
            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully',
                'data' => ['fcm_token' => $token]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving FCM token: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}