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
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $validator = Validator::make($request->all(), [
            'food_id' => ['required', 'integer', Rule::exists('foods', 'id')],
            'rfid_uid' => ['required', 'string', 'max:255', Rule::exists('users', 'rfid_uid')],
            'quantity' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'date' => ['required', 'date_format:Y-m-d'],
            'meal_type' => ['required', 'string', 'in:Breakfast,Lunch,Dinner,Snacks'],
            'total_calories' => ['required', 'numeric', 'min:0'],
            'total_protein' => ['required', 'numeric', 'min:0'],
            'total_fats' => ['required', 'numeric', 'min:0'],
            'total_carbs' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $foodLog = FoodLog::create([
                'food_id' => $request->food_id,
                'rfid_uid' => $request->rfid_uid,
                'quantity' => $request->quantity,
                'date' => $request->date,
                'meal_type' => $request->meal_type,
                'total_calories' => $request->total_calories,
                'total_protein' => $request->total_protein,
                'total_fats' => $request->total_fats,
                'total_carbs' => $request->total_carbs,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Food logged successfully',
                'data' => $foodLog->load('food') // Eager load food relationship
            ], 201);

        } catch (\Exception $e) {
            Log::error('Food log error', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user' => $user->id
            ]);
            
            return $this->serverErrorResponse($e);
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
            'id' => $log->id,
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
public function destroy($id)
{
    $user = auth('sanctum')->user();
    if (!$user) {
        return $this->unauthorizedResponse();
    }

    try {
        $foodLog = FoodLog::where('id', $id)
            ->where('rfid_uid', $user->rfid_uid)
            ->firstOrFail();

        $foodLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food log deleted successfully'
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Food log not found or unauthorized'
        ], 404);
        
    } catch (\Exception $e) {
        Log::error('Delete food log error', [
            'error' => $e->getMessage(),
            'food_log_id' => $id,
            'user' => $user->id
        ]);
        
        return $this->serverErrorResponse($e);
    }
}



}