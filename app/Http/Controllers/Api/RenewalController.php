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
            \Log::error('Renewal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Renewal failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function uploadPayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'amount' => 'required|numeric|min:0',
            'membership_type' => 'required|string',
        ]);

        try {
            // Handle the image upload
            if ($request->hasFile('payment_screenshot')) {
                $file = $request->file('payment_screenshot');
                $filename = 'payment_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/payments', $filename, 'public');

                // Find user by RFID
                $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

                // Save to members_payments table
                $payment = MembersPayment::create([
                    'rfid_uid' => $user->rfid_uid,
                    'amount' => $request->amount,
                    'payment_method' => 'gcash', // Hardcoded as GCash since this is for screenshot uploads
                    'payment_screenshot' => $path,
                    'status' => 'pending',
                    'payment_date' => now(),
                    'payment_reference' => null,
                ]);

                // Optionally, link to renewals table if needed
                $renewal = Renewal::where('rfid_uid', $user->rfid_uid)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($renewal) {
                    $renewal->update([
                        'payment_screenshot' => $path,
                        'payment_method' => 'gcash',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment screenshot uploaded successfully',
                    'data' => [
                        'payment_id' => $payment->id,
                        'screenshot_path' => $path,
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No screenshot provided'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Error uploading payment screenshot: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment screenshot: ' . $e->getMessage()
            ], 500);
        }
    }
}
