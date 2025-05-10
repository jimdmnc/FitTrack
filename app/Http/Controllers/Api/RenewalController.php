<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Renewal;
use App\Models\MembersPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;


class RenewalController extends Controller
{




    public function renewMembershipApp(Request $request)
    {
        // Validate request
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'membership_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_method' => 'required|in:cash,gcash',
            'amount' => 'required|numeric|min:0',
            'payment_screenshot' => 'nullable|string|required_if:payment_method,gcash',
        ]);
    
        // Find user by RFID
        $user = User::where('rfid_uid', $request->rfid_uid)->first();
    
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    
        try {
            // Update user membership - both payment methods will be pending approval
            $user->update([
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'member_status' => 'expired', 
                'session_status' => 'pending',
                'needs_approval' => 1,
            ]);
    
            // Process payment screenshot if provided
            $paymentScreenshotPath = $request->payment_screenshot;
    
            // Create Renewal and Payment records
            $renewal = Renewal::create([
                'rfid_uid' => $user->rfid_uid,
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_reference' => null,
                'payment_screenshot' => $paymentScreenshotPath,
            ]);
    
            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
                'payment_reference' => null,
                'payment_screenshot' => $paymentScreenshotPath,
                'status' => 'pending',
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Renewal request submitted. Waiting for staff approval.',
                'user' => $user,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Renewal failed: ' . $e->getMessage(),
            ], 400);
        }
    }
    




    public function uploadPayment(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amount' => 'required|string',
            'membership_type' => 'required|string',
            'rfid' => 'required|string'
        ]);
    
        try {
            // Store the image
            $path = $request->file('image')->store('payment_screenshots', 'public');
            
            // Get user ID from RFID
            $user = User::where('rfid_uid', $request->rfid)->firstOrFail();
            
            // Save to database
            $payment = MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $request->amount,
                'membership_type' => $request->membership_type,
                'screenshot_path' => $path,
                'status' => 'pending'
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Payment uploaded successfully',
                'data' => $payment
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment: ' . $e->getMessage()
            ], 500);
        }
    }





}
