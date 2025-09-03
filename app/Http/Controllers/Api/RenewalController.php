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
use Illuminate\Support\Facades\Schema;

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
            // Update only membership_type and status fields, not dates
            $user->update([
                'membership_type' => $request->membership_type,
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
                'payment_screenshot' => $paymentScreenshotPath,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Renewal request submitted. Waiting for staff approval.',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            \Log::error('Renewal error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
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
            // Verify members_payments table schema
            $requiredColumns = ['rfid_uid', 'amount', 'payment_method', 'payment_screenshot', 'status', 'payment_date'];
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn('members_payment', $column)) {
                    \Log::error("Missing column in members_payments: $column");
                    return response()->json([
                        'success' => false,
                        'message' => "Server error: Missing column '$column' in members_payment table"
                    ], 500);
                }
            }

            // Verify renewals table schema
            $renewalColumns = ['payment_screenshot', 'payment_method'];
            foreach ($renewalColumns as $column) {
                if (!Schema::hasColumn('renewals', $column)) {
                    \Log::error("Missing column in renewals: $column");
                    return response()->json([
                        'success' => false,
                        'message' => "Server error: Missing column '$column' in renewals table"
                    ], 500);
                }
            }

            // Handle the image upload
            if ($request->hasFile('payment_screenshot')) {
                $file = $request->file('payment_screenshot');
                $filename = 'payment_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/payments', $filename, 'public');

                // Find user by RFID
                $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

                // Save to members_payments table
                try {
                    $payment = MembersPayment::create([
                        'rfid_uid' => $user->rfid_uid,
                        'amount' => $request->amount,
                        'payment_method' => 'gcash',
                        'payment_screenshot' => $path,
                        'status' => 'pending',
                        'payment_date' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create MembersPayment: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                    throw new \Exception('MembersPayment creation failed: ' . $e->getMessage());
                }

                // Update renewals table
                try {
                    $renewal = Renewal::where('rfid_uid', $user->rfid_uid)
                        ->where('payment_method', 'gcash')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($renewal) {
                        $renewal->update([
                            'payment_screenshot' => $path,
                            'payment_method' => 'gcash',
                        ]);
                    } else {
                        \Log::warning("No pending renewal found for rfid_uid: {$user->rfid_uid}");
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to update Renewal: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                    throw new \Exception('Renewal update failed: ' . $e->getMessage());
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
            \Log::error('Error uploading payment screenshot: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment screenshot: ' . $e->getMessage()
            ], 500);
        }
    }
}