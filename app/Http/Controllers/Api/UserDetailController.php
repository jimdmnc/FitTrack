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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                [
                    'new_weight' => $validator->validated()['weight'],
                    'updated_at' => now()->toDateTimeString()
                ]
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








    public function uploadPaymentScreenshot(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'payment_screenshot' => 'required|image|max:2048', // Max 2MB
        ]);

        try {
            if ($request->hasFile('payment_screenshot')) {
                // Store the file
                $file = $request->file('payment_screenshot');
                $filename = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store in the public 'payment_screenshots' folder
                $path = $file->storeAs('payment_screenshots', $filename, 'public');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'filePath' => $path
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renew membership from mobile app
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            // Update user membership - both payment methods will be pending approval
            $user->update([
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
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
                'payment_reference' => null,
                'payment_screenshot' => $paymentScreenshotPath,
                'status' => 'pending',
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Renewal request submitted. Waiting for staff approval.',
                'user' => $user,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Renewal failed: ' . $e->getMessage(),
            ], 400);
        }
    }
    





    public function upload(Request $request)
    {
        // Validate request
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'path' => 'required|string',
            'is_public' => 'required|boolean'
        ]);

        try {
            $image = $request->file('image');
            $path = $request->input('path', 'uploads');
            $isPublic = $request->boolean('is_public', false);
            
            // Create a unique filename
            $extension = $image->getClientOriginalExtension();
            $filename = time() . '_' . Str::random(10) . '.' . $extension;
            
            // Store the file
            $disk = $isPublic ? 'public' : 'local';
            $fullPath = $path . '/' . $filename;
            
            // Upload the file
            $image->storeAs($path, $filename, $disk);
            
            // Generate the URL
            $url = $isPublic 
                ? asset(Storage::url($fullPath))  // For public storage
                : null;                           // For private storage, no direct URL
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image_url' => $url,
                'path' => $fullPath
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }




/**
 * Get payment history
 */
public function getPaymentHistory($rfid_uid)
{
    if (empty($rfid_uid)) {
        return $this->errorResponse('RFID UID is required', null, 400);
    }
    
    $history = DB::table('members_payment')
        ->where('rfid_uid', $rfid_uid)
        ->orderBy('payment_date', 'desc')
        ->get(['id', 'rfid_uid', 'amount', 'payment_method', 'payment_date', 'created_at', 'updated_at']);
        
    return response()->json($history);
}





   

        
}
