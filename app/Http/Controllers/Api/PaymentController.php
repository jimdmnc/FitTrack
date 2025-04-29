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
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'rfid_uid' => 'required|string',
            'membership_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        DB::beginTransaction();
        
        try {
            // Create payment record first with 'pending' status
            $payment = MembersPayment::create([
                'rfid_uid' => $request->rfid_uid,
                'amount' => $request->amount,
                'payment_method' => 'gcash',
                'payment_date' => now(),
            ]);

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

            // Update payment with source ID
            $payment->update([
                'payment_reference' => $source['id'], // You might want to add this column
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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status and update database
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'source_id' => 'required|string',
            'payment_id' => 'required|integer',
        ]);

        DB::beginTransaction();
        
        try {
            $source = $this->paymongoService->verifyPayment($request->source_id);
            $isPaid = $source['attributes']['status'] === 'chargeable';
            
            $payment = MembersPayment::findOrFail($request->payment_id);
            
            if ($isPaid) {
                // Update payment record
                $payment->update([
                    'amount' => $source['attributes']['amount'] / 100,
                    'status' => 'completed', // You might want to add this column
                ]);
                
                // Process membership renewal
                $metadata = $source['attributes']['metadata'];
                
                // TODO: Add your membership renewal logic here
                // This would update the member's expiration date
                // Example:
                // Member::where('rfid_uid', $metadata['rfid_uid'])
                //     ->update(['expiration_date' => $metadata['end_date']]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'paid' => $isPaid,
                'status' => $source['attributes']['status'],
                'amount' => $source['attributes']['amount'] / 100,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}