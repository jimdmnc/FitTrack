<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodSeeder extends Seeder {
    public function run(): void {
        Food::insert([
            ['foodName' => 'Apple', 'calories' => 0.52, 'protein' => 0.003, 'fats' => 0.002, 'carbs' => 0.14, 'grams' => 1],
            ['foodName' => 'Banana', 'calories' => 0.89, 'protein' => 0.011, 'fats' => 0.003, 'carbs' => 0.23, 'grams' => 1],
            ['foodName' => 'Chicken Breast', 'calories' => 1.65, 'protein' => 0.31, 'fats' => 0.036, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Rice (White, Cooked)', 'calories' => 1.3, 'protein' => 0.027, 'fats' => 0.003, 'carbs' => 0.28, 'grams' => 1],
            ['foodName' => 'Egg (Whole)', 'calories' => 1.43, 'protein' => 0.126, 'fats' => 0.095, 'carbs' => 0.007, 'grams' => 1],
            
            // Additional protein-rich foods popular among gym-goers
            ['foodName' => 'Whey Protein Powder', 'calories' => 4, 'protein' => 0.8, 'fats' => 0.07, 'carbs' => 0.08, 'grams' => 1],
            ['foodName' => 'Greek Yogurt', 'calories' => 0.59, 'protein' => 0.10, 'fats' => 0.005, 'carbs' => 0.038, 'grams' => 1],
            ['foodName' => 'Salmon (Fillet)', 'calories' => 2.08, 'protein' => 0.22, 'fats' => 0.13, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Tuna (Canned in Water)', 'calories' => 1.16, 'protein' => 0.25, 'fats' => 0.01, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Sardines (Canned in Oil)', 'calories' => 2.08, 'protein' => 0.24, 'fats' => 0.12, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Turkey Breast', 'calories' => 1.04, 'protein' => 0.24, 'fats' => 0.01, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Cottage Cheese', 'calories' => 0.98, 'protein' => 0.11, 'fats' => 0.045, 'carbs' => 0.032, 'grams' => 1],
            
            // Carbohydrate sources for energy
            ['foodName' => 'Sweet Potato', 'calories' => 0.86, 'protein' => 0.016, 'fats' => 0.001, 'carbs' => 0.20, 'grams' => 1],
            ['foodName' => 'Oatmeal (Dry)', 'calories' => 3.79, 'protein' => 0.135, 'fats' => 0.068, 'carbs' => 0.677, 'grams' => 1],
            ['foodName' => 'Quinoa (Cooked)', 'calories' => 1.20, 'protein' => 0.044, 'fats' => 0.019, 'carbs' => 0.21, 'grams' => 1],
            ['foodName' => 'Whole Wheat Bread', 'calories' => 2.65, 'protein' => 0.11, 'fats' => 0.03, 'carbs' => 0.49, 'grams' => 1],
            ['foodName' => 'Brown Rice (Cooked)', 'calories' => 1.12, 'protein' => 0.023, 'fats' => 0.009, 'carbs' => 0.23, 'grams' => 1],
            
            // Healthy fats
            ['foodName' => 'Avocado', 'calories' => 1.60, 'protein' => 0.02, 'fats' => 0.15, 'carbs' => 0.085, 'grams' => 1],
            ['foodName' => 'Almonds', 'calories' => 5.79, 'protein' => 0.21, 'fats' => 0.49, 'carbs' => 0.22, 'grams' => 1],
            ['foodName' => 'Peanut Butter', 'calories' => 5.88, 'protein' => 0.25, 'fats' => 0.50, 'carbs' => 0.22, 'grams' => 1],
            ['foodName' => 'Olive Oil', 'calories' => 8.84, 'protein' => 0, 'fats' => 1.0, 'carbs' => 0, 'grams' => 1],
            
            // Vegetables for micronutrients
            ['foodName' => 'Broccoli', 'calories' => 0.34, 'protein' => 0.028, 'fats' => 0.004, 'carbs' => 0.07, 'grams' => 1],
            ['foodName' => 'Spinach', 'calories' => 0.23, 'protein' => 0.029, 'fats' => 0.004, 'carbs' => 0.036, 'grams' => 1],
            ['foodName' => 'Kale', 'calories' => 0.49, 'protein' => 0.032, 'fats' => 0.009, 'carbs' => 0.10, 'grams' => 1],
            
            // Supplements and sports nutrition
            ['foodName' => 'Creatine Monohydrate', 'calories' => 0, 'protein' => 0, 'fats' => 0, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'BCAA Powder', 'calories' => 4.1, 'protein' => 1.0, 'fats' => 0, 'carbs' => 0, 'grams' => 1],
            ['foodName' => 'Protein Bar', 'calories' => 3.33, 'protein' => 0.20, 'fats' => 0.09, 'carbs' => 0.40, 'grams' => 1]
        
        ]);
    }
}
