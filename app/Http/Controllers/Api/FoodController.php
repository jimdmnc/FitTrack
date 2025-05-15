<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller {
    protected function unauthorizedResponse() {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    protected function validationErrorResponse($validator) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    protected function serverErrorResponse(\Exception $e) {
        Log::error('FoodController error', [
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

    public function index(Request $request) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        try {
            $foodLogs = FoodLog::where('rfid_uid', $request->rfid_uid)->get();
            return response()->json([
                'success' => true,
                'data' => $foodLogs
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    public function storeFoodLog(Request $request) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $validator = Validator::make($request->all(), [
            'food_name' => ['required', 'string', 'max:255'],
            'rfid_uid' => ['required', 'string', 'max:255'],
            'meal_type' => ['required', 'string', 'in:Breakfast,Lunch,Dinner,Snacks'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
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
                'food_name' => $request->food_name,
                'rfid_uid' => $request->rfid_uid,
                'meal_type' => $request->meal_type,
                'quantity' => $request->quantity,
                'date' => $request->date,
                'consumed_calories' => $request->consumed_calories,
                'consumed_protein' => $request->consumed_protein,
                'consumed_fats' => $request->consumed_fats,
                'consumed_carbs' => $request->consumed_carbs,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Food log created',
                'data' => $foodLog
            ], 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }

    public function destroyFoodLog(Request $request, $id) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        try {
            $foodLog = FoodLog::find($id);
            if (!$foodLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Food log not found'
                ], 404);
            }

            if ($foodLog->rfid_uid !== $request->rfid_uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this food log'
                ], 403);
            }

            $foodLog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Food log deleted'
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }



public function getFoodLogsByDate(Request $request) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $date = $request->query('date', now()->format('Y-m-d'));

        $validator = Validator::make(['date' => $date, 'rfid_uid' => $request->rfid_uid], [
            'date' => ['required', 'date'],
            'rfid_uid' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $logs = FoodLog::where('rfid_uid', $request->rfid_uid)
                ->whereDate('date', $date)
                ->get();

            $formattedLogs = $logs->map(function ($log) {
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