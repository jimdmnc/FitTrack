<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MembershipRenewal;
use App\Models\Payment;

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
            'user_id' => 'required',
            'membership_type' => 'required',
        ]);

        try {
            $source = $this->paymongoService->createGcashSource(
                $request->amount,
                $request->description,
                [
                    'user_id' => $request->user_id,
                    'membership_type' => $request->membership_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]
            );

            return response()->json([
                'success' => true,
                'redirect_url' => $source['attributes']['redirect']['checkout_url'],
                'source_id' => $source['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'source_id' => 'required|string',
        ]);

        try {
            $source = $this->paymongoService->verifyPayment($request->source_id);
            
            return response()->json([
                'success' => true,
                'paid' => $source['attributes']['status'] === 'chargeable',
                'status' => $source['attributes']['status'],
                'amount' => $source['attributes']['amount'] / 100,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle PayMongo webhook
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('Paymongo-Signature');
        $payload = $request->getContent();
        
        // In production, you should verify the webhook signature
        // For testing, we'll skip verification
        
        $event = json_decode($payload, true);
        
        if ($event['data']['attributes']['type'] === 'source.chargeable') {
            $sourceId = $event['data']['attributes']['data']['id'];
            $source = $this->paymongoService->verifyPayment($sourceId);
            
            if ($source['attributes']['status'] === 'chargeable') {
                // Process the payment and update your database
                $metadata = $source['attributes']['metadata'];
                
                // TODO: Update your membership record here
                // Example:
                // Membership::create([
                //     'user_id' => $metadata['user_id'],
                //     'type' => $metadata['membership_type'],
                //     'start_date' => $metadata['start_date'],
                //     'end_date' => $metadata['end_date'],
                //     'status' => 'active',
                //     'payment_method' => 'gcash',
                //     'amount' => $source['attributes']['amount'] / 100,
                // ]);
                
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Event not handled']);
    }


    
}