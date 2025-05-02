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
            'grams' => 'required|numeric',
        ]);

        $food = Food::create($validated);

        return response()->json($food, 201);
    }

    public function index(Request $request)
    {
        $query = $request->query('query');
        
        $foods = Food::when($query, function($q) use ($query) {
            return $q->where('foodName', 'like', '%'.$query.'%');
        })->get();

        return response()->json($foods);
    }
    


// In your Laravel controller
    // Search foods by name
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $foods = Food::where('foodName', 'like', '%'.$request->query.'%')
                        ->limit(50) // Prevent too many results
                        ->get();

        return response()->json($foods);
    }

}