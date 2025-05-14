<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use Illuminate\Support\Facades\Log;

class FoodController extends Controller
{
    // Helper for consistent error responses
    protected function serverErrorResponse(\Exception $e)
    {
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
        $validated = $request->validate([
            'foodName' => 'required|string|max:255',
            'consumedCalories' => 'required|numeric',
            'consumedProtein' => 'required|numeric',
            'consumedFats' => 'required|numeric',
            'consumedCarbs' => 'required|numeric',
            'grams' => 'required|numeric',
        ]);
    
        $food = Food::create($validated);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $food->id,
                'foodName' => $food->foodName,
                'consumedCalories' => $food->consumedCalories,
                'consumedProtein' => $food->consumedProtein,
                'consumedFats' => $food->consumedFats,
                'consumedCarbs' => $food->consumedCarbs,
                'grams' => $food->grams,
            ],
        ]);
    }

    public function search(Request $request)
    {
        try {
            $query = $request->query('query');

            if (!$query || strlen($query) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query must be at least 3 characters long'
                ], 400);
            }

            $foods = Food::where('foodName', 'like', '%' . $query . '%')->get();

            $formattedFoods = $foods->map(function ($food) {
                return [
                    'id' => $food->id,
                    'foodName' => $food->foodName,
                    'consumedCalories' => $food->consumed_calories,
                    'consumedProtein' => $food->consumed_protein,
                    'consumedFats' => $food->consumed_fats,
                    'consumedCarbs' => $food->consumed_carbs,
                    'grams' => $food->grams,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedFoods
            ]);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e);
        }
    }
}
