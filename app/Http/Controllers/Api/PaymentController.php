<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembersPayment;
use App\Models\User; // Make sure to import your User model
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymongoService;

    public function __construct(PayMongoService $paymongoService)
    {
        $this->paymongoService = $paymongoService;
    }

    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'rfid_uid' => 'required|string|exists:users,rfid_uid',
            'membership_type' => 'required|in:7,30,365',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'payment_method' => 'required|in:gcash,cash', // Add payment method validation
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
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
            ]);

            if ($request->payment_method === 'gcash') {
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
            } else {
                // For cash payments, immediately process the membership
                $this->processMembershipRenewal($payment);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Cash payment recorded successfully',
                    'payment_id' => $payment->id,
                ]);
            }

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
            'source_id' => 'required_without:payment_id|string',
            'payment_id' => 'required|integer|exists:members_payment,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payment = MembersPayment::findOrFail($request->payment_id);

            // For cash payments, return immediately
            if ($payment->payment_method === 'cash') {
                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'status' => 'completed',
                    'amount' => $payment->amount,
                ]);
            }

            // For GCash payments, verify with PayMongo
            $source = $this->paymongoService->verifyPayment($request->source_id);
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
        // Update user's membership
        $user = User::where('rfid_uid', $payment->rfid_uid)->firstOrFail();
        
        // Calculate end date based on membership type
        $endDate = now();
        switch ($payment->membership_type) {
            case '7':
                $endDate = $endDate->addDays(7);
                break;
            case '30':
                $endDate = $endDate->addDays(30);
                break;
            case '365':
                $endDate = $endDate->addDays(365);
                break;
        }

        $user->update([
            'membership_expiry' => $endDate,
            'membership_type' => $payment->membership_type,
        ]);
    }
}