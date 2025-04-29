<?php

namespace App\Http\Controllers\Api;

use App\Models\MembersPayment;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymongoService;

    public function __construct(PayMongoService $paymongoService)
    {
        $this->paymongoService = $paymongoService;
    }

    /**
     * Create GCash payment source
     */
    public function createGcashPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'rfid_uid' => 'required|string|exists:members,rfid_uid',
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
            // Create payment record
            $payment = MembersPayment::create([
                'rfid_uid' => $request->rfid_uid,
                'amount' => $request->amount,
                'payment_method' => 'gcash',
                'status' => 'pending',
            ]);

            // Create GCash source
            $source = $this->paymongoService->createGcashSource(
                $request->amount,
                $request->description,
                [
                    'payment_id' => $payment->id,
                    'rfid_uid' => $request->rfid_uid,
                    'membership_type' => $request->membership_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]
            );

            // Update payment with reference
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
            report($e); // Log the error
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * @bodyParam source_id string required PayMongo source ID
     * @bodyParam payment_id integer required Local payment ID
     */
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
                
                // TODO: Add membership renewal logic here
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

}