<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDetail;

class UserCalorieController extends Controller
{
    public function getCalories(Request $request)
    {
        $user = $request->user();
        $details = UserDetail::where('rfid_uid', $user->rfid_uid)->first();

        if (!$details) {
            return response()->json(['message' => 'User details not found.'], 404);
        }

        $bmr = $this->calculateBMR($details);
        $tdee = $this->adjustForActivity($bmr, $details->activity_level);
        $calorieGoal = $this->adjustForGoal($tdee, $details->goal);
        $macros = $this->calculateMacros($calorieGoal);

        return response()->json([
            'calorie_goal' => round($calorieGoal),
            'macros' => $macros,
        ]);
    }

    private function calculateBMR($details)
    {
        if ($details->gender === 'Male') {
            return 10 * $details->weight + 6.25 * $details->height - 5 * $details->age + 5;
        } else {
            return 10 * $details->weight + 6.25 * $details->height - 5 * $details->age - 161;
        }
    }

    private function adjustForActivity($bmr, $level)
    {
        return match ($level) {
            'Beginner' => $bmr * 1.2,
            'Intermediate' => $bmr * 1.55,
            'Advanced' => $bmr * 1.9,
            default => $bmr,
        };
    }

    private function adjustForGoal($calories, $goal)
    {
        return match ($goal) {
            'Gain Muscle' => $calories + 300,
            'Lose Weight' => $calories - 300,
            'Maintain' => $calories,
            default => $calories,
        };
    }

    private function calculateMacros($calories)
    {
        $proteinCal = $calories * 0.3;
        $carbsCal = $calories * 0.4;
        $fatsCal = $calories * 0.3;

        return [
            'protein_g' => round($proteinCal / 4),
            'carbs_g' => round($carbsCal / 4),
            'fats_g' => round($fatsCal / 9),
        ];
    }
}
