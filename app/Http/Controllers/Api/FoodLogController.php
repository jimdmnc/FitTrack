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
    // Helper methods for consistent responses
    protected function unauthorizedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    protected function validationErrorResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    protected function serverErrorResponse(\Exception $e)
    {
        Log::error('FoodLogController error', [
            'error' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
            'request' => request()->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Server error',
            'error' => $e->getMessage()
        ], 500);
    }

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
            'consumed_calories' => ['required', 'numeric', 'min:0'],
            'consumed_protein' => ['required', 'numeric', 'min:0'],
            'consumed_fats' => ['required', 'numeric', 'min:0'],
            'consumed_carbs' => ['required', 'numeric', 'min:0'],
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
                'consumed_calories' => $request->consumed_calories,
                'consumed_protein' => $request->consumed_protein,
                'consumed_fats' => $request->consumed_fats,
                'consumed_carbs' => $request->consumed_carbs,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Food logged successfully',
                'data' => [
                    'id' => $foodLog->id,
                    'foodName' => $foodLog->food ? $foodLog->food->foodName : null,
                    'mealType' => $foodLog->meal_type,
                    'quantity' => (float)$foodLog->quantity,
                    'consumedCalories' => (float)$foodLog->consumed_calories,
                    'consumedProtein' => (float)$foodLog->consumed_protein,
                    'consumedFats' => (float)$foodLog->consumed_fats,
                    'consumedCarbs' => (float)$foodLog->consumed_carbs,
                    'date' => $foodLog->date->format('Y-m-d'),
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    // public function getFoodLogsByDate(Request $request)
    // {
    //     $user = auth('sanctum')->user();
    //     if (!$user) {
    //         return $this->unauthorizedResponse();
    //     }

    //     $date = $request->query('date', now()->format('Y-m-d'));

    //     try {
    //         $logs = FoodLog::with('food')
    //             ->where('rfid_uid', $user->rfid_uid)
    //             ->whereDate('date', $date)
    //             ->get();

    //         $formattedLogs = $logs->map(function ($log) {
    //             return [
    //                 'id' => $log->id,
    //                 'food_name' => $log->food ? $log->food->foodName : null,
    //                 'mealType' => $log->meal_type,
    //                 'quantity' => (float)$log->quantity,
    //                 'consumedCalories' => (float)$log->consumed_calories,
    //                 'consumedProtein' => (float)$log->consumed_protein,
    //                 'consumedFats' => (float)$log->consumed_fats,
    //                 'consumedCarbs' => (float)$log->consumed_carbs,
    //                 'date' => $log->date->format('Y-m-d'),
    //             ];
    //         });

    //         return response()->json($formattedLogs);
    //     } catch (\Exception $e) {
    //         return $this->serverErrorResponse($e);
    //     }
    // }

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
            return $this->serverErrorResponse($e);
        }
    }

    public function getAllFoodLogs(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        try {
            $foodLogs = FoodLog::with('food')
                ->where('rfid_uid', $user->rfid_uid)
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedLogs = $foodLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'food_name' => $log->food_name,
                    'mealType' => $log->meal_type,
                    'quantity' => (float)$log->quantity,
                    'consumed_calories' => (float)$log->consumed_calories,
                    'consumed_protein' => (float)$log->consumed_protein,
                    'consumed_fats' => (float)$log->consumed_fats,
                    'consumed_carbs' => (float)$log->consumed_carbs,
                    'date' => $log->date->format('Y-m-d'),
                ];
            });

            return response()->json($formattedLogs);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
}
