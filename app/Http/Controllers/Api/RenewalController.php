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
        // === VALIDATION ===
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'membership_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payment_method' => 'required|in:cash,gcash',
            'amount' => 'required|numeric|min:0',
            'payment_screenshot' => 'required_if:payment_method,gcash|nullable|string',
        ]);
    
        $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();
    
        $isGcash = $request->payment_method === 'gcash';
        $screenshotPath = null;
    
        // === HANDLE GCASH SCREENSHOT ===
        if ($isGcash && $request->payment_screenshot) {
            if (preg_match('/^data:image\/(\w+);base64,/', $request->payment_screenshot)) {
                $image = $request->payment_screenshot;
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
    
                $fileName = 'gcash_' . $user->rfid_uid . '_' . time() . '.jpg';
                $path = 'payments/gcash/' . $fileName;
                \Storage::disk('public')->put($path, $imageData);
                $screenshotPath = $path;
            } else {
                $screenshotPath = $request->payment_screenshot;
            }
        }
    
        DB::beginTransaction();
    
        try {
            // === UPDATE USER (both go to pending) ===
            $user->update([
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'member_status' => 'expired',
                'session_status' => 'pending',
                'needs_approval' => 1,
            ]);
    
            // === CREATE RENEWAL RECORD ===
            Renewal::create([
                'rfid_uid' => $user->rfid_uid,
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
                'status' => 'pending', // BOTH PENDING
                'payment_screenshot' => $screenshotPath,
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => $isGcash
                    ? 'GCash payment submitted. Waiting for staff to verify screenshot.'
                    : 'On-site renewal submitted. Waiting for staff approval.',
                'requires_approval' => true,
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('App Renewal Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Renewal failed. Please try again.',
            ], 500);
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
