<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\API\UserDetailController;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/user/details', [UserDetailController::class, 'store']);  // Save user details
    Route::get('/user/details', [UserDetailController::class, 'show']);  // Get user details
});




Route::post('/attendance', [RFIDController::class, 'handleAttendance']);
Route::post('/save_rfid', [RFIDController::class, 'saveRFID']);
// Route to fetch the latest RFID UID
Route::get('/rfid/latest', [RFIDController::class, 'getLatestRFID']);
