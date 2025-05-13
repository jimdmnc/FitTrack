<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        try {
            $user = Auth::user();
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user profile: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch profile'], 500);
        }
    }

    public function uploadProfileImage(Request $request)
    {
        try {
            $request->validate([
                'profile_image' => 'required|string',
                'rfid_uid' => 'required|string|exists:users,rfid_uid',
            ]);

            $user = User::where('rfid_uid', $request->rfid_uid)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->profile_image = $request->profile_image;
            $user->save();

            return response()->json(['message' => 'Profile image uploaded successfully']);
        } catch (\Exception $e) {
            Log::error('Error uploading profile image: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload image'], 500);
        }
    }
}