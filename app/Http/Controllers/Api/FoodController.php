<?php
// Hypothetical App\Http\Controllers\Api\FoodController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
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

    public function store(Request $request) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $validator = Validator::make($request->all(), [
            'food_name' => ['required', 'string', 'max:255'],
            'consumed_calories' => ['required', 'numeric', 'min:0'],
            'consumed_protein' => ['required', 'numeric', 'min:0'],
            'consumed_fats' => ['required', 'numeric', 'min:0'],
            'consumed_carbs' => ['required', 'numeric', 'min:0'],
            'grams' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $food = Food::create([
                'food_name' => $request->food_name,
                'consumed_calories' => $request->consumed_calories,
                'consumed_protein' => $request->consumed_protein,
                'consumed_fats' => $request->consumed_fats,
                'consumed_carbs' => $request->consumed_carbs,
                'grams' => $request->grams,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $food->id,
                    'food_name' => $food->food_name,
                    'consumed_calories' => (float)$food->consumed_calories,
                    'consumed_protein' => (float)$food->consumed_protein,
                    'consumed_fats' => (float)$food->consumed_fats,
                    'consumed_carbs' => (float)$food->consumed_carbs,
                    'grams' => (float)$food->grams,
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
}