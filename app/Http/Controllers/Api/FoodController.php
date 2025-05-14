<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'foodName' => 'required|string|max:255',
            'calories' => 'required|numeric',
            'protein' => 'required|numeric',
            'fats' => 'required|numeric',
            'carbs' => 'required|numeric',
            'grams' => 'required|integer',
        ]);

        $food = Food::create($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $food->id,
                'foodName' => $food->foodName,
                'calories' => $food->calories,
                'protein' => $food->protein,
                'fats' => $food->fats,
                'carbs' => $food->carbs,
                'grams' => $food->grams,
            ]
        ], 201);
    }

    public function index(Request $request)
    {
        $query = $request->query('query');
        
        $foods = Food::when($query, function($q) use ($query) {
            return $q->where('foodName', 'like', '%'.$query.'%');
        })->get();

        return response()->json($foods);
    }
    


    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:3'
            ]);
    
            $foods = Food::where('foodName', 'like', '%'.$request->input('query').'%')
                            ->get();
    
            return response()->json([
                'success' => true,
                'data' => $foods
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}