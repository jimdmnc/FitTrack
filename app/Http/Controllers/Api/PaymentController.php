<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembersPayment;
use App\Models\User;
use App\Services\PayMongoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymongoService;

    public function __construct(PayMongoService $paymongoService)
    {
        $this->paymongoService = $paymongoService;
    }

    public function createGcashPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'rfid_uid' => 'required|string|exists:users,rfid_uid',
            'membership_type' => 'required|in:7,30,365',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $payment = MembersPayment::create([
                'rfid_uid' => $request->rfid_uid,
                'amount' => $request->amount,
                'payment_method' => 'gcash',
                'status' => 'pending',
                'metadata' => $this->preparePaymentMetadata($request, $payment->id), // Add this line
            ]);

            $source = $this->paymongoService->createGcashSource(
                $request->amount,
                $request->description,
                $this->preparePaymentMetadata($request, $payment->id)
            );

            $payment->update([
                'payment_reference' => $source['id'],
            ]);

            DB::commit();

            // For test mode, automatically handle payment activation
            if ($this->paymongoService->isTestMode()) {
                $this->handleTestPaymentActivation($source['id'], $this->preparePaymentMetadata($request, $payment->id));
            }

            return response()->json([
                'success' => true,
                'redirect_url' => $source['attributes']['redirect']['checkout_url'],
                'source_id' => $source['id'],
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_id' => 'required|string',
            'payment_id' => 'required|integer|exists:members_payment,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $source = $this->paymongoService->verifyPayment($request->source_id);
            $payment = MembersPayment::findOrFail($request->payment_id);

            $status = $source['attributes']['status'];
            $isPaid = ($status === 'chargeable');

            if ($isPaid) {
                $payment->update([
                    'status' => 'completed',
                    'amount' => $source['attributes']['amount'] / 100,
                ]);
                
                // Process the membership activation
                $this->activateUserMembership($request->source_id, $payment);
            }

            return response()->json([
                'success' => true,
                'paid' => $isPaid,
                'status' => $status,
                'amount' => $source['attributes']['amount'] / 100,
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Status check failed'
            ], 500);
        }
    }

    private function preparePaymentMetadata(Request $request, int $paymentId): array
    {
        return [
            'payment_id' => $paymentId,
            'rfid_uid' => $request->rfid_uid,
            'membership_type' => $request->membership_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'amount' => $request->amount,
            'description' => $request->description,
        ];
    }

    /**
     * Handle test payment activation immediately after creation
     * 
     * @param string $sourceId
     * @param array $metadata
     * @return void
     */
    private function handleTestPaymentActivation(string $sourceId, array $metadata)
    {
        try {
            // Verify the test payment immediately
            $verifiedData = $this->paymongoService->verifyPayment($sourceId, 1);
            
            if ($verifiedData['attributes']['status'] === 'chargeable') {
                // Fetch the payment record
                $payment = MembersPayment::where('payment_reference', $sourceId)->first();
                if ($payment) {
                    $this->activateUserMembership($sourceId, $payment);
                }
            }
        } catch (\Exception $e) {
            Log::error('Test payment activation failed', [
                'source_id' => $sourceId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Activate user membership based on payment data
     * 
     * @param string $sourceId
     * @param MembersPayment $payment
     * @return void
     */
    private function activateUserMembership(string $sourceId, array $metadat)
    {
        try {
            $user = User::where('rfid_uid', $payment->rfid_uid)->first();
            
            if ($user) {
                // Get metadata from payment or source verification
                $metadata = $payment->metadata ?? [];
                
                // Use metadata['membership_type'] instead of $payment->membership_type
                $membershipType = $metadata['membership_type'] ?? '7'; // default 7 days
                $startDate = $metadata['start_date'] ?? now()->toDateString();
                $endDate = $metadata['end_date'] ?? $this->calculateEndDate($membershipType, $startDate);
    
                $updateData = [
                    'member_status' => 'active',
                    'session_status' => 'approved',
                    'needs_approval' => 0,
                    'membership_type' => $membershipType, // This now correctly reads from metadata
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
    
                $user->update($updateData);
    
                Log::info('Membership fully activated for user', [
                    'user_id' => $user->id,
                    'rfid_uid' => $user->rfid_uid,
                    'source_id' => $sourceId,
                    'payment_id' => $payment->id,
                    'update_data' => $updateData,
                    'test_mode' => $this->paymongoService->isTestMode()
                ]);
            } else {
                Log::warning('User not found for membership activation', [
                    'rfid_uid' => $payment->rfid_uid,
                    'payment_id' => $payment->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Membership activation failed', [
                'payment_id' => $payment->id,
                'rfid_uid' => $payment->rfid_uid,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    /**
     * Calculate membership end date based on type and start date
     * 
     * @param string $membershipType
     * @param string $startDate
     * @return string
     */
    private function calculateEndDate(string $membershipType, string $startDate): string
    {
        $days = (int)$membershipType;
        return Carbon::parse($startDate)->addDays($days)->toDateString();
    }
}