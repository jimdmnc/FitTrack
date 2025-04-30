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
                
                $this->processMembershipRenewal($payment);
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

    private function processMembershipRenewal(MembersPayment $payment)
    {
        $user = User::where('rfid_uid', $payment->rfid_uid)->first();
        
        if (!$user) {
            Log::error('User not found for membership renewal', [
                'payment_id' => $payment->id,
                'rfid_uid' => $payment->rfid_uid
            ]);
            return;
        }

        $updateData = [
            'member_status' => 'active',
            'session_status' => 'approved',
            'needs_approval' => 0,
            'membership_type' => $payment->metadata['membership_type'] ?? $user->membership_type,
            'start_date' => $payment->metadata['start_date'] ?? now()->toDateString(),
            'end_date' => $payment->metadata['end_date'] ?? $this->calculateEndDate(
                $payment->metadata['membership_type'] ?? '7',
                $payment->metadata['start_date'] ?? now()->toDateString()
            )
        ];

        $user->update($updateData);

        Log::info('Membership activated for user', [
            'user_id' => $user->id,
            'rfid_uid' => $user->rfid_uid,
            'payment_id' => $payment->id,
            'update_data' => $updateData
        ]);
    }

    private function calculateEndDate(string $membershipType, string $startDate): string
    {
        $days = (int)$membershipType;
        return Carbon::parse($startDate)->addDays($days)->toDateString();
    }
}