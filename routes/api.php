<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/saveWalkthroughData', [UserDetailController::class, 'store']);
});

Route::post('/attendance', [RFIDController::class, 'handleAttendance']);
Route::post('/save_rfid', [RFIDController::class, 'saveRFID']);
Route::get('/rfid/latest', [RFIDController::class, 'getLatestRFID']);
Route::delete('/rfid/clear/{uid}', [RFIDController::class, 'clear']);
