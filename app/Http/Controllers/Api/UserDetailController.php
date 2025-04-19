<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    // Store or update user details
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'gender' => 'required|in:Male,Female,Other',  // Enum values should match case exactly
            'activity_level' => 'required|in:Beginner,Intermediate,Advanced',  // Enum values case-sensitive
            'age' => 'required|integer|min:10|max:100',
            'height' => 'required|numeric|min:100|max:250',
            'weight' => 'required|numeric|min:30|max:300',
            'target_muscle' => 'nullable|string|max:255',  // target_muscle is nullable
            'goal' => 'required|in:Gain Muscle,Lose Weight,Maintain',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user(); // Get authenticated user

        // Check if user has rfid_uid
        if (!$user->rfid_uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID UID not found for the user'
            ], 400);
        }

        try {
            // Prepare data from validated request
            $data = $validator->validated();

            // Ensure 'target_muscle' is set to null if not provided
            $data['target_muscle'] = $data['target_muscle'] ?? null;

            // Store or update the user details based on the rfid_uid
            $userDetail = UserDetail::updateOrCreate(
                ['rfid_uid' => $user->rfid_uid],  // Match RFID UID from authenticated user
                $data
            );

            return response()->json([
                'status' => 'success',
                'message' => 'User details saved successfully',
                'data' => $userDetail
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update user details (reuse store logic)
    public function update(Request $request)
    {
        return $this->store($request);
    }

    // Get user details
    public function show()
    {
        $user = Auth::user(); // Get authenticated user

        // Check if user has rfid_uid
        if (!$user->rfid_uid) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID UID not found for the user'
            ], 400);
        }

        // Retrieve user details based on RFID UID
        $userDetail = UserDetail::where('rfid_uid', $user->rfid_uid)->first();

        // Check if user details exist
        if (!$userDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'User details not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $userDetail
        ]);
    }

     /**
     * Get user details by RFID UID
     */
    public function getDetailsByRfid($rfid)
    {
        // First validate the RFID format if needed
        if (empty($rfid)) {
            return response()->json([
                'success' => false,
                'message' => 'RFID UID is required'
            ], 400);
        }

        $details = UserDetail::where('rfid_uid', $rfid)->first();

        if (!$details) {
            return response()->json([
                'success' => false,
                'message' => 'User details not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'age' => $details->age,
                'gender' => $details->gender,
                'weight' => $details->weight,
                'height' => $details->height,
                'activity_level' => $details->activity_level,
                'goal' => $details->goal,
                'target_muscle' => $details->target_muscle
            ]
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
    
    



}
