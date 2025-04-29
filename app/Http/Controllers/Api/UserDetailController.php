<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Models\WeightLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

use App\Models\User;

use App\Models\Renewal;
use App\Models\MembersPayment;
class UserDetailController extends Controller
{
   // Common validation rules
   protected $userDetailRules = [
    'gender' => 'required|in:Male,Female,Other',
    'activity_level' => 'required|in:Beginner,Intermediate,Advanced',
    'age' => 'required|integer|min:10|max:100',
    'height' => 'required|numeric|min:100|max:250',
    'weight' => 'required|numeric|min:30|max:300',
    'target_muscle' => 'nullable|string|max:255',
    'goal' => 'required|in:Gain Muscle,Lose Weight,Maintain',
];

/**
 * Store or update user details
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), $this->userDetailRules);

    if ($validator->fails()) {
        return $this->errorResponse('Validation failed', $validator->errors(), 422);
    }

    $user = Auth::user();
    if (!$user->rfid_uid) {
        return $this->errorResponse('RFID UID not found for the user', null, 400);
    }

    try {
        $data = $validator->validated();
        $data['target_muscle'] = $data['target_muscle'] ?? null;

        $userDetail = UserDetail::updateOrCreate(
            ['rfid_uid' => $user->rfid_uid],
            $data
        );

        return $this->successResponse(
            'User details saved successfully',
            $userDetail,
            201
        );
    } catch (\Exception $e) {
        return $this->errorResponse('Failed to save user details', $e->getMessage(), 500);
    }
}

/**
 * Update user details (alias for store)
 */
public function update(Request $request)
{
    return $this->store($request);
}

/**
 * Get authenticated user's details
 */
public function show()
{
    $user = Auth::user();
    if (!$user->rfid_uid) {
        return $this->errorResponse('RFID UID not found for the user', null, 400);
    }

    $userDetail = UserDetail::where('rfid_uid', $user->rfid_uid)->first();
    if (!$userDetail) {
        return $this->errorResponse('User details not found', null, 404);
    }

    return $this->successResponse(null, $userDetail);
}

     /**
     * Get user details by RFID UID
     */
    public function getDetailsByRfid($rfid)
    {
        if (empty($rfid)) {
            return $this->errorResponse('RFID UID is required', null, 400);
        }

        $details = UserDetail::where('rfid_uid', $rfid)->first();
        if (!$details) {
            return $this->errorResponse('User details not found', null, 404);
        }

        return $this->successResponse(null, [
            'age' => $details->age,
            'gender' => $details->gender,
            'weight' => $details->weight,
            'height' => $details->height,
            'activity_level' => $details->activity_level,
            'goal' => $details->goal,
            'target_muscle' => $details->target_muscle
        ]);
    }

    public function getDailyCalories(Request $request)
    {
        $user = $request->user(); // from Sanctum
        $details = UserDetail::where('rfid_uid', $user->rfid_uid)->first();
    
        if (!$details) {
            return response()->json(['error' => 'User details not found.'], 404);
        }
    
        $age = $details->age;
        $gender = $details->gender;
        $weight = $details->weight;
        $height = $details->height;
        $activity = $details->activity_level;
        $goal = $details->goal;
    
        // BMR calculation
        $bmr = ($gender === 'Male')
            ? 10 * $weight + 6.25 * $height - 5 * $age + 5
            : 10 * $weight + 6.25 * $height - 5 * $age - 161;
    
        // TDEE (Total Daily Energy Expenditure)
        $multiplier = match ($activity) {
            'Beginner' => 1.2,
            'Intermediate' => 1.55,
            'Advanced' => 1.9,
            default => 1.2,
        };
        $tdee = $bmr * $multiplier;
    
        if ($goal === 'Gain Muscle') {
            $tdee += 300;
        } elseif ($goal === 'Lose Weight') {
            $tdee -= 300;
        }
    
        $dailyCalories = (int) $tdee;
    
        // Macronutrient goals (percentage-based)
        $proteinGrams = ($dailyCalories * 0.30) / 4;
        $fatsGrams = ($dailyCalories * 0.25) / 9;
        $carbsGrams = ($dailyCalories * 0.45) / 4;
    
        return response()->json([
            'daily_calories' => $dailyCalories,
            'protein' => (int) $proteinGrams,
            'fats' => (int) $fatsGrams,
            'carbs' => (int) $carbsGrams,
        ]);
    }
    
    

/**
     * Update user weight
     */
    public function updateWeight(Request $request, $rfid)
    {
        $validator = Validator::make($request->all(), [
            'weight' => 'required|numeric|min:20|max:300'
        ]);
    
        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors(), 422);
        }
    
        $user = UserDetail::where('rfid_uid', $rfid)->first();
        if (!$user) {
            return $this->errorResponse('User not found', null, 404);
        }
    
        try {
            DB::transaction(function () use ($rfid, $validator) {
                $validated = $validator->validated();
                UserDetail::where('rfid_uid', $rfid)
                    ->update(['weight' => $validated['weight']]);
    
                WeightLog::create([
                    'rfid_uid' => $rfid,
                    'weight' => $validated['weight'],
                    'log_date' => now()->format('Y-m-d')
                ]);
            });
    
            return $this->successResponse('Weight updated successfully', [
                'new_weight' => $validator->validated()['weight'],
                'updated_at' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update weight', $e->getMessage(), 500);
        }
    }
    
    /**
     * Get weight history
     */
    public function getWeightHistory($rfid_uid)
    {
        if (empty($rfid_uid)) {
            return $this->errorResponse('RFID UID is required', null, 400);
        }

        $history = WeightLog::where('rfid_uid', $rfid_uid)
            ->orderBy('log_date', 'desc')
            ->get(['weight', 'log_date']);
    
            return response()->json($history);
        }

    /**
     * Standard success response
     */
    protected function successResponse($message = null, $data = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Standard error response
     */
    protected function errorResponse($message, $error = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $error
        ], $code);
    }








    
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
            ]);
        
            // Find user by RFID
            $user = User::where('rfid_uid', $request->rfid_uid)->first();
        
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
        
            $isCash = $request->payment_method === 'cash';
            $sessionStatus = $isCash ? 'pending' : 'approved';
            $needsApproval = $isCash ? 1 : 0;
        
            // Update user membership
            if (!$isCash) {
                // GCash payment → direct update membership dates
                $user->update([
                    'membership_type' => $request->membership_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'member_status' => 'active',
                    'session_status' => $sessionStatus,
                    'needs_approval' => $needsApproval,
                ]);
            } else {
                // Cash payment → no start_date and end_date update yet
                $user->update([
                    'membership_type' => $request->membership_type,
                    'member_status' => 'expired',
                    'session_status' => $sessionStatus,
                    'needs_approval' => $needsApproval,
                ]);
            }
        
            // Create Renewal and Payment records
            Renewal::create([
                'rfid_uid' => $user->rfid_uid,
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'payment_method' => $request->payment_method,
                'status' => $sessionStatus,
            ]);
        
            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
            ]);
        
            return response()->json([
                'success' => true,
                'message' => $isCash 
                    ? 'Renewal request submitted. Waiting for staff approval.'
                    : 'Membership renewed successfully!',
                'user' => $user,
            ]);
        }



        // public function getPaymentHistory(Request $request)
        // {
        //     $validator = Validator::make($request->all(), [
        //         'rfid_uid' => 'required|string|exists:users,rfid_uid'
        //     ]);
    
        //     if ($validator->fails()) {
        //         return $this->errorResponse('Validation failed', $validator->errors(), 422);
        //     }
            
        //     // Check if user has access to this RFID UID (either admin or own RFID)
        //     $user = Auth::user();
        //     if ($user->rfid_uid !== $request->rfid_uid && !$user->hasRole('admin')) {
        //         return $this->errorResponse('Unauthorized access to member data', null, 403);
        //     }
        
        //     try {
        //         $payments = MembersPayment::where('rfid_uid', $request->rfid_uid)
        //             ->orderBy('payment_date', 'desc')
        //             ->get()
        //             ->map(function ($payment) {
        //                 return [
        //                     'id' => $payment->id,
        //                     'rfid_uid' => $payment->rfid_uid,
        //                     'amount' => (float) $payment->amount,
        //                     'payment_method' => $payment->payment_method,                            
        //                     'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
        //                     'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
        //                     'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
        //                 ];
        //             });
            
        //         return $this->successResponse(null, [
        //             'data' => $payments,
        //             'total_amount' => $payments->sum('amount'),
        //             'count' => $payments->count()
        //         ]);
                
        //     } catch (\Exception $e) {
        //         return $this->errorResponse('Failed to retrieve payment history', $e->getMessage(), 500);
        //     }
        // }





        public function getPaymentHistory($rfid_uid)
        {
            // Check if rfid_uid is provided either in route parameter or request
            if (empty($rfid_uid)) {
                $rfid_uid = $request->input('rfid_uid');
                if (empty($rfid_uid)) {
                    return $this->errorResponse('RFID UID is required', null, 400);
                }
            }
            
            // Validate RFID exists in database
            $userExists = \App\Models\User::where('rfid_uid', $rfid_uid)->exists();
            if (!$userExists) {
                return $this->errorResponse('RFID UID not found', null, 404);
            }
        
            try {
                // Start building the query
                $query = MembersPayment::where('rfid_uid', $rfid_uid)
                    ->orderBy('payment_date', 'desc');
                
                // Search functionality (optional)
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('amount', 'like', "%$search%")
                          ->orWhere('payment_method', 'like', "%$search%")
                          ->orWhere('payment_date', 'like', "%$search%");
                    });
                }
                
                // Filter by payment method (optional)
                if ($request->filled('payment_method')) {
                    $query->where('payment_method', $request->payment_method);
                }
                
                // Filter by time range (optional)
                if ($request->filled('time_filter')) {
                    $today = Carbon::today();
                    switch ($request->time_filter) {
                        case 'today':
                            $query->whereDate('payment_date', $today);
                            break;
                        case 'week':
                            $query->whereBetween('payment_date', [
                                $today->copy()->startOfWeek(),
                                $today->copy()->endOfWeek()
                            ]);
                            break;
                        case 'month':
                            $query->whereBetween('payment_date', [
                                $today->copy()->startOfMonth(),
                                $today->copy()->endOfMonth()
                            ]);
                            break;
                    }
                }
        
                // Get the results
                $payments = $query->get();
                
                // Map the payment data similar to WeightHistory format
                $formattedPayments = $payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'rfid_uid' => $payment->rfid_uid,
                        'amount' => (float) $payment->amount,
                        'payment_method' => $payment->payment_method,                            
                        'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
                    ];
                });
            
                // Return JSON response like WeightHistory
                return response()->json([
                    'data' => $formattedPayments,
                    'total_amount' => $formattedPayments->sum('amount'),
                    'count' => $formattedPayments->count()
                ]);
                
            } catch (\Exception $e) {
                return $this->errorResponse('Failed to retrieve payment history', $e->getMessage(), 500);
            }
        }

        
}
