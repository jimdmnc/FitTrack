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
            'meal_type' => 'required|string|in:Breakfast,Lunch,Dinner,Snacks', // Add meal type validation
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
                'food_id' => $validated['food_id'],
                'rfid_uid' => $validated['rfid_uid'],
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'meal_type' => $validated['meal_type'], // Add meal type
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
// app/Http/Controllers/FoodLogController.php
public function getFoodLogsByDate(Request $request)
{
    // Get the date from the query string or default to today's date
    $date = $request->query('date', now()->format('Y-m-d'));

    // Retrieve the food logs for the authenticated user and specific date, eager load the related 'food'
    $logs = FoodLog::with('food')
        ->where('rfid_uid', auth()->user()->rfid_uid) // Filter by user RFID
        ->whereDate('date', $date) // Filter by date
        ->get();

    // Format the logs to include the 'foodName' from the related 'food' table
    $formattedLogs = $logs->map(function ($log) {
        return [
            // 'id' => $log->id,
            'food_id' => $log->food_id,
            'foodName' => $log->food ? $log->food->foodName : null, // Make sure food_name is being sent
            'meal_type' => $log->meal_type,
            'quantity' => (float)$log->quantity,
            'total_calories' => (float)$log->total_calories,
            'total_protein' => (float)$log->total_protein,
            'total_fats' => (float)$log->total_fats,
            'total_carbs' => (float)$log->total_carbs,
            'date' => $log->date->format('Y-m-d'), // Format the date to 'Y-m-d'
        ];
    });

    // Return the formatted food logs as a JSON response
    return response()->json([
        'food_logs' => $formattedLogs
    ]);
}


}