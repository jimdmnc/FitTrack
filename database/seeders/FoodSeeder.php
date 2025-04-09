<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodSeeder extends Seeder {
    public function run(): void {
        Food::insert([
            ['foodName' => 'Apple', 'calories' => 0.52, 'protein' => 0.005, 'fats' => 0.003, 'carbs' => 0.14, 'grams' => 1],
            ['foodName' => 'Banana', 'calories' => 1.05, 'protein' => 0.01, 'fats' => 0.03, 'carbs' => 0.27, 'grams' => 1],
            ['foodName' => 'Chicken Breast', 'calories' => 1.7, 'protein' => 0.3, 'fats' => 0.0, 'carbs' => 0.0, 'grams' => 1],
            ['foodName' => 'Rice', 'calories' => 1.3, 'protein' => 0.027, 'fats' => 0.002, 'carbs' => 0.31, 'grams' => 1],
            ['foodName' => 'Egg', 'calories' => 1.56, 'protein' => 0.126, 'fats' => 0.106, 'carbs' => 0.0112, 'grams' => 1],
        ]);
    }
}
