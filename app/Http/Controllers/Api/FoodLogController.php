<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodLog;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FoodLogController extends Controller
{
    public function logFood(Request $request)
    {
        // Add Sanctum authentication check
        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'food_id' => ['required', 'integer', Rule::exists('foods', 'id')],
            'rfid_uid' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01|max:1000',
            'date' => 'required|date_format:Y-m-d',
            // Include the calculated fields in validation
            'total_calories' => 'required|numeric',
            'total_protein' => 'required|numeric',
            'total_fats' => 'required|numeric',
            'total_carbs' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();
            
            // Get the authenticated user
            $user = auth('sanctum')->user();
            
            $foodLog = FoodLog::create([
                // 'user_id' => $user->id, // Associate with user
                'food_id' => $validated['food_id'],
                'rfid_uid' => $validated['rfid_uid'],
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'total_calories' => $validated['total_calories'],
                'total_protein' => $validated['total_protein'],
                'total_fats' => $validated['total_fats'],
                'total_carbs' => $validated['total_carbs'],
            ]);

            return response()->json([
                'message' => 'Food logged successfully',
                'data' => $foodLog
            ], 201);

        } catch (\Exception $e) {
            Log::error('Food log error: '.$e->getMessage());
            Log::error('Request data: '.json_encode($request->all()));
            Log::error('Stack trace: '.$e->getTraceAsString());
            
            return response()->json([
                'message' => 'Server error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}