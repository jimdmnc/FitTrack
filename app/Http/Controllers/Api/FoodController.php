<?php
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

    public function index(Request $request) {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $query = $request->query('search', '');
        $foods = Food::where('food_name', 'LIKE', "%$query%")
            ->get(['id', 'food_name', 'consumed_calories', 'consumed_protein', 'consumed_fats', 'consumed_carbs', 'grams']);

        return response()->json($foods, 200);
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
            $food = Food::firstOrCreate(
                ['food_name' => $request->food_name],
                [
                    'consumed_calories' => $request->consumed_calories,
                    'consumed_protein' => $request->consumed_protein,
                    'consumed_fats' => $request->consumed_fats,
                    'consumed_carbs' => $request->consumed_carbs,
                    'grams' => $request->grams
                ]
            );

            return response()->json($food, 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
}