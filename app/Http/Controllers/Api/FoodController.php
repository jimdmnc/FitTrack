<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
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

    public function store(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $validator = Validator::make($request->all(), [
            'food_id' => ['required_without:food_name', 'integer', 'exists:foods,id'],
            'food_name' => ['required_without:food_id', 'string', 'max:255'],
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
            $foodId = $request->food_id;
            if ($request->food_name) {
                $food = Food::firstOrCreate(
                    ['food_name' => $request->food_name],
                    [
                        'consumed_calories' => $request->consumed_calories,
                        'consumed_protein' => $request->consumed_protein,
                        'consumed_fats' => $request->consumed_fats,
                        'consumed_carbs' => $request->consumed_carbs,
                        'grams' => 100
                    ]
                );
                $foodId = $food->id;
            }

            $foodLog = FoodLog::create([
                'food_id' => $foodId,
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
                'message' => 'Food log created successfully',
                'data' => $foodLog
            ], 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
}