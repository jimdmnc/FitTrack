<?php
use Illuminate\Http\Request;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\Api\UserDetailController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserCalorieController;
use App\Http\Controllers\UsdaFoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\PriceApiController;
use App\Http\Controllers\Api\UserDetailsController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\FoodLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\RenewalController;

use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ViewMembersController;
use App\Http\Controllers\Staff\PaymentTrackingController;
use App\Http\Controllers\Api\NotificationController;
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {



    Route::post('/user/upload-profile-image', [AuthController::class, 'uploadProfileImage']);


    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/details/{rfid_uid}', [UserDetailController::class, 'getDetailsByRfid']);
    Route::post('/saveWalkthroughData', [UserDetailController::class, 'store']);
    Route::get('/daily-calories', [UserDetailController::class, 'getDailyCalories']);





    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/calories', [UserCalorieController::class, 'getCalories']);




    // Route::get('/attendance/{rfid_uid}', [AttendanceController::class, 'getUserAttendance']);

    Route::get('/attendance/{rfid}/{date}', function ($rfid, $date) {
        return response()->json(
            DB::table('attendances')
                ->where('attendance_date', $date)
                ->where('rfid_uid', $rfid)
                ->select(
                    'time_in',
                    'time_out',
                    'attendance_date',
                    'status'
                )
                ->get()
        );
    });

    Route::get('/attendance/all', function (Request $request) {
        $rfid = $request->query('rfid_uid');
        return response()->json(
            DB::table('attendances')
                ->where('rfid_uid', $rfid)
                ->get()
        );
    });
    

    Route::post('/update-weight/{rfid}', [UserDetailController::class, 'updateWeight']);

    Route::get('/weight-history/{rfid_uid}', [UserDetailController::class, 'getWeightHistory']);


    Route::get('/payment-history/{rfid_uid}', [UserDetailController::class, 'getPaymentHistory']);


    Route::post('/renew-membershipApp', [RenewalController::class, 'renewMembershipApp']);
    Route::post('/upload-payment-screenshot', [RenewalController::class, 'uploadPayment']);


    Route::post('/change-password', [AuthController::class, 'changePassword']);

    
    // Route::apiResource('foods', FoodController::class);

    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

   


    // Food routes
    Route::post('/foods', [FoodController::class, 'store']);
    Route::get('/foods/search', [FoodController::class, 'search']);

    // Food log routes
    Route::post('/food_logs', [FoodLogController::class, 'logFood']);
    // Route::get('/food_logs', [FoodController::class, 'index']);
    Route::post('/food_logs', [FoodController::class, 'storeFoodLog']);
    Route::delete('/food_logs/{id}', [FoodLogController::class, 'destroy']);
    Route::get('/food_logs/all', [FoodLogController::class, 'getAllFoodLogs']);


    // Route::get('/foods', [FoodController::class, 'index']);

    Route::post('/log-food', [FoodLogController::class, 'logFood']); // Log food
    // Route to get food logs by date
    Route::get('/food_logs', [FoodController::class, 'getFoodLogsByDate']);

    Route::post('/save-token', [AuthController::class, 'saveToken']);
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
});

Route::get('/prices', [PriceApiController::class, 'index']);

Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
->middleware(['guest'])
->name('password.email');

Route::post('/attendance', [RFIDController::class, 'processRFID']);
// Route::post('/save_rfid', [RFIDController::class, 'saveRFID']);
Route::get('/rfid/latest', [RFIDController::class, 'getLatestRFID']);
Route::delete('/rfid/clear/{uid}', [RFIDController::class, 'clear']);
Route::middleware('auth:sanctum')->get('/user/birthdate', function (Request $request) {
    return response()->json([
        'birthdate' => $request->user()->birthdate, // e.g., "1998-05-20"
    ]);
});