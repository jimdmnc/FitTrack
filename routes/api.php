<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserCalorieController;
use App\Http\Controllers\Api\UserDetailsController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\FoodLogController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/saveWalkthroughData', [UserDetailController::class, 'store']);
    Route::get('/calories', [UserCalorieController::class, 'getCalories']);
    Route::get('/daily-calories', [UserDetailController::class, 'getDailyCalories']);

    Route::get('/foods', [FoodController::class, 'index']);

    Route::post('/log-food', [FoodLogController::class, 'logFood']); // Log food
    // Route to get food logs by date
    Route::get('/food-logs', [FoodLogController::class, 'getFoodLogsByDate']);
    Route::delete('/food-logs/{id}', [FoodLogController::class, 'destroy'])
    ->name('food-logs.destroy');
 
});




Route::post('/attendance', [RFIDController::class, 'handleAttendance']);
Route::post('/save_rfid', [RFIDController::class, 'saveRFID']);
Route::get('/rfid/latest', [RFIDController::class, 'getLatestRFID']);
Route::delete('/rfid/clear/{uid}', [RFIDController::class, 'clear']);
