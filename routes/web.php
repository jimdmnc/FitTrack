<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RFIDController;


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\MembershipRegistrationController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ViewmembersController;
use App\Http\Controllers\Staff\PaymentTrackingController;
use App\Http\Controllers\Staff\ConnectHardwareController;
use App\Http\Controllers\Staff\ReportController;

use App\Http\Controllers\Member\MemberDashboardController;


// Public routes
Route::get('/', function () {
    return view('welcome');
});
Route::get('/latest-uid', [RFIDController::class, 'getLatestUid'])->name('latest.rfid');
Route::get('/get-latest-rfid', function () {
    $latestRfid = DB::table('rfid_tags')->orderBy('created_at', 'desc')->first();
    
    return response()->json([
        'uid' => $latestRfid ? $latestRfid->uid : null
    ]);
});
// Authenticated routes
Route::middleware('auth')->group(function () {
    // Staff routes
    Route::prefix('staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
        // Membership Registration
        Route::get('/membershipRegistration', [MembershipRegistrationController::class, 'index'])->name('staff.membershipRegistration');
        Route::post('/membershipRegistration', [MembershipRegistrationController::class, 'store'])->name('staff.membershipRegistration');        

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('staff.attendance');
        Route::get('/viewmembers', [ViewmembersController::class, 'index'])->name('staff.viewmembers');
        Route::get('/paymentTracking', [PaymentTrackingController::class, 'index'])->name('staff.paymentTracking');
        Route::get('/connectHardware', [ConnectHardwareController::class, 'index'])->name('staff.connectHardware');
        Route::get('/report', [ReportController::class, 'index'])->name('staff.report');
            // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('member')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('members.dashboard');

    });
});

// Authentication routes (login, register, etc.)
require __DIR__.'/auth.php';