<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    public function run()
    {
        DB::table('foods')->insert([
            // Fruits
            ['foodName' => 'Mango (Ripe)', 'calories' => 0.6, 'protein' => 0.008, 'fats' => 0.004, 'carbs' => 0.15, 'grams' => 1],
            ['foodName' => 'Banana (Lakatan)', 'calories' => 0.89, 'protein' => 0.011, 'fats' => 0.003, 'carbs' => 0.23, 'grams' => 1],
            
            // Rice & Noodles
            ['foodName' => 'White Rice (Kanin)', 'calories' => 1.3, 'protein' => 0.027, 'fats' => 0.002, 'carbs' => 0.31, 'grams' => 1],
            ['foodName' => 'Pancit Canton', 'calories' => 1.38, 'protein' => 0.048, 'fats' => 0.042, 'carbs' => 0.24, 'grams' => 1],
            
            // Meat & Seafood
            ['foodName' => 'Lechon Kawali', 'calories' => 3.1, 'protein' => 0.22, 'fats' => 0.24, 'carbs' => 0.0, 'grams' => 1],
            ['foodName' => 'Bangus (Milkfish)', 'calories' => 1.68, 'protein' => 0.2, 'fats' => 0.09, 'carbs' => 0.0, 'grams' => 1],
            ['foodName' => 'Chicken Adobo', 'calories' => 1.95, 'protein' => 0.18, 'fats' => 0.12, 'carbs' => 0.04, 'grams' => 1],
            
            // Common Dishes
            ['foodName' => 'Sinigang na Baboy', 'calories' => 0.78, 'protein' => 0.068, 'fats' => 0.048, 'carbs' => 0.032, 'grams' => 1],
            ['foodName' => 'Kare-Kare', 'calories' => 1.45, 'protein' => 0.092, 'fats' => 0.11, 'carbs' => 0.042, 'grams' => 1],
            
            // Snacks & Desserts
            ['foodName' => 'Halo-Halo', 'calories' => 1.2, 'protein' => 0.025, 'fats' => 0.035, 'carbs' => 0.24, 'grams' => 1],
            ['foodName' => 'Pandesal', 'calories' => 2.63, 'protein' => 0.083, 'fats' => 0.017, 'carbs' => 0.5, 'grams' => 1],
            
            // Breakfast Items
            ['foodName' => 'Tapsilog', 'calories' => 1.85, 'protein' => 0.15, 'fats' => 0.095, 'carbs' => 0.12, 'grams' => 1],
            ['foodName' => 'Longganisa', 'calories' => 3.33, 'protein' => 0.18, 'fats' => 0.28, 'carbs' => 0.042, 'grams' => 1],
            
            // Vegetables
            ['foodName' => 'Kangkong', 'calories' => 0.19, 'protein' => 0.027, 'fats' => 0.002, 'carbs' => 0.038, 'grams' => 1],
            ['foodName' => 'Sitaw (String Beans)', 'calories' => 0.31, 'protein' => 0.018, 'fats' => 0.002, 'carbs' => 0.07, 'grams' => 1]
        ]);
    }
}