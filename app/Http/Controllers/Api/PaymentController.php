<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PayMongoService;
use App\Models\User;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $payMongoService;

    public function __construct(PayMongoService $payMongoService)
    {
        $this->payMongoService = $payMongoService;
    }

    /**
     * Create a payment source and redirect to PayMongo checkout
     */
    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1',
                'user_id' => 'required|exists:users,id',
                'membership_type' => 'required|string',
                'start_date' => 'nullable|date',
                'description' => 'nullable|string'
            ]);

            $user = User::findOrFail($validated['user_id']);
            
            // Prepare metadata that will be used to update user after payment
            $metadata = [
                'user_id' => $user->id,
                'rfid_uid' => $user->rfid_uid ?? null,
                'membership_type' => $validated['membership_type'],
                'start_date' => $validated['start_date'] ?? now()->toDateString(),
                'payment_for' => 'membership',
                'request_id' => uniqid('pay_')
            ];
            
            // Create the GCash source
            $source = $this->payMongoService->createGcashSource(
                $validated['amount'],
                $validated['description'] ?? "Membership payment for {$user->name}",
                $metadata
            );
            
            // Store source information for tracking
            PaymentTransaction::create([
                'source_id' => $source['id'],
                'user_id' => $user->id,
                'rfid_uid' => $user->rfid_uid ?? null,
                'amount' => $validated['amount'],
                'status' => 'pending',
                'payment_method' => 'gcash',
                'description' => $validated['description'] ?? "Membership payment",
                'metadata' => $metadata
            ]);
            
            // Redirect to checkout URL
            return redirect($source['attributes']['redirect']['checkout_url']);
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     * This is called when user is redirected back from PayMongo after
     * clicking "Authorize Payment"
     */
    public function success(Request $request)
    {
        try {
            // Extract source ID from the request
            $sourceId = $request->query('source_id');
            
            if (!$sourceId) {
                throw new \Exception('Source ID not found in redirect');
            }
            
            // Log the success redirect
            Log::info('Payment success redirect received', [
                'source_id' => $sourceId,
                'request' => $request->all()
            ]);
            
            // Find the payment transaction
            $transaction = PaymentTransaction::where('source_id', $sourceId)->first();
            
            if (!$transaction) {
                throw new \Exception('Payment transaction not found');
            }
            
            // Verify the payment source status
            $source = $this->payMongoService->verifyPayment($sourceId);
            $status = $source['attributes']['status'] ?? 'unknown';
            
            // Update transaction status
            $transaction->update([
                'status' => $status
            ]);
            
            // For test mode, we need to manually create a payment since webhooks aren't available
            if ($status === 'chargeable') {
                try {
                    // Create a payment from the source
                    $payment = $this->payMongoService->createPayment($sourceId);
                    
                    // Update transaction with payment ID
                    $transaction->update([
                        'payment_id' => $payment['id'],
                        'status' => 'paid'
                    ]);
                    
                    // Now activate the membership since payment is successful
                    $this->activateUserMembership($transaction);
                    
                    return redirect()->route('payment.thankyou', ['status' => 'success'])
                        ->with('success', 'Payment successful! Your membership has been activated.');
                } catch (\Exception $e) {
                    Log::error('Test payment creation failed', [
                        'source_id' => $sourceId,
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('payment.thankyou', ['status' => 'pending'])
                        ->with('warning', 'Your payment is being processed. Membership will be activated shortly.');
                }
            } else if ($status === 'paid') {
                // Payment already processed
                $this->activateUserMembership($transaction);
                
                return redirect()->route('payment.thankyou', ['status' => 'success'])
                    ->with('success', 'Payment successful! Your membership has been activated.');
            } else {
                return redirect()->route('payment.thankyou', ['status' => 'pending'])
                    ->with('warning', 'Your payment is being processed. Membership will be activated shortly.');
            }
        } catch (\Exception $e) {
            Log::error('Payment success handler failed', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('payment.thankyou', ['status' => 'error'])
                ->with('error', 'There was an issue processing your payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle failed payment
     * This is called when user is redirected back from PayMongo after
     * clicking "Failed" button
     */
    public function failed(Request $request)
    {
        $sourceId = $request->query('source_id');
        
        if ($sourceId) {
            // Find and update transaction status
            $transaction = PaymentTransaction::where('source_id', $sourceId)->first();
            
            if ($transaction) {
                $transaction->update(['status' => 'failed']);
            }
            
            Log::info('Payment failed', [
                'source_id' => $sourceId,
                'request' => $request->all()
            ]);
        }
        
        return redirect()->route('payment.thankyou', ['status' => 'failed'])
            ->with('error', 'Your payment was not successful. Please try again.');
    }

    /**
     * Thank you page after payment flow
     */
    public function thankYou(Request $request)
    {
        $status = $request->query('status', 'unknown');
        return view('payment.thankyou', compact('status'));
    }

    /**
     * Activate user membership based on payment transaction
     */
    protected function activateUserMembership(PaymentTransaction $transaction)
    {
        $user = User::find($transaction->user_id);
        $metadata = $transaction->metadata;
        
        if (!$user) {
            Log::error('User not found for membership activation', [
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id
            ]);
            return;
        }
        
        // Calculate dates based on membership type
        $startDate = $metadata['start_date'] ?? now()->toDateString();
        $membershipType = $metadata['membership_type'] ?? '7';
        $endDate = $metadata['end_date'] ?? $this->calculateEndDate($membershipType, $startDate);

        $updateData = [
            'member_status' => 'active',
            'session_status' => 'approved',
            'needs_approval' => 0,
            'membership_type' => $membershipType,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $user->update($updateData);

        Log::info('Membership fully activated for user', [
            'user_id' => $user->id,
            'rfid_uid' => $user->rfid_uid ?? 'none',
            'transaction_id' => $transaction->id,
            'update_data' => $updateData
        ]);
    }

    /**
     * Calculate end date based on membership type
     */
    private function calculateEndDate(string $membershipType, string $startDate): string
    {
        $days = (int)$membershipType;
        return Carbon::parse($startDate)->addDays($days)->toDateString();
    }
    
    /**
     * Check payment status (for AJAX polling)
     */
    public function checkStatus(Request $request)
    {
        $sourceId = $request->input('source_id');
        
        if (!$sourceId) {
            return response()->json(['status' => 'error', 'message' => 'Source ID required'], 400);
        }
        
        $transaction = PaymentTransaction::where('source_id', $sourceId)->first();
        
        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }
        
        return response()->json([
            'status' => $transaction->status,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'date' => $transaction->updated_at->format('Y-m-d H:i:s'),
        ]);
    }
}