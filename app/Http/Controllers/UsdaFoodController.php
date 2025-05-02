<?php

// app/Http/Controllers/UsdaFoodController.php
namespace App\Http\Controllers;

use App\Models\FoodList; // Or your existing food model
use Illuminate\Http\Request;

class UsdaFoodController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        // You could implement local caching of USDA foods here
        return response()->json([]); // Or return cached results
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'fats' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'grams' => 'required|numeric|min:0'
        ]);

        $food = FoodList::create($validated);

        return response()->json($food, 201);
    }
}