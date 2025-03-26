<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RFIDController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\MembershipRegistrationController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ViewmembersController;
use App\Http\Controllers\Staff\PaymentTrackingController;
use App\Http\Controllers\Staff\ReportController;

use App\Http\Controllers\Member\MemberDashboardController;


// Public routes
Route::get('/', function () {
    return view('welcome');
});



// Route to handle attendance
Route::post('/rfid/attendance', [RFIDController::class, 'handleAttendance']);

Route::middleware('auth')->group(function () {
    // Staff routes
    Route::prefix('staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
        Route::get('/peak-hours', [DashboardController::class, 'getPeakHours']);

        // Membership Registration
        Route::get('/membershipRegistration', [MembershipRegistrationController::class, 'index'])
            ->name('staff.membershipRegistration');
        Route::post('/membershipRegistration', [MembershipRegistrationController::class, 'store'])
            ->name('staff.membershipRegistration.store');


        Route::post('/renew-membership', [ViewmembersController::class, 'renewMembership'])
            ->name('renew.membership');

        Route::get('/attendance', [AttendanceController::class, 'index'])
            ->name('staff.attendance');
        Route::get('/staff/attendance', [AttendanceController::class, 'index'])
            ->name('staff.attendance.index');

        // Route to record attendance
        Route::post('/staff/record-attendance', [AttendanceController::class, 'recordAttendance'])
            ->name('staff.record-attendance');

        Route::get('/viewmembers', [ViewmembersController::class, 'index'])->name('staff.viewmembers');
       
       
       
       
       
        Route::get('/paymentTracking', [PaymentTrackingController::class, 'index'])
            ->name('staff.paymentTracking');
        Route::post('/staff/paymentTracking/store', [PaymentTrackingController::class, 'store'])
            ->name('payments.store');
        Route::put('/staff/paymentTracking/update/{id}', [PaymentTrackingController::class, 'update'])
            ->name('payments.update');
        Route::delete('/staff/paymentTracking/destroy/{id}', [PaymentTrackingController::class, 'destroy'])
            ->name('payments.destroy');






        // Report routes
        Route::get('/report', [ReportController::class, 'index'])->name('staff.report');
        Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('generate.report');

    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Member routes
    Route::prefix('member')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('members.dashboard');
    });
});

// Authentication routes (login, register, etc.)
require __DIR__.'/auth.php';