<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\RFIDController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\MembershipRegistrationController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\ViewmembersController;
use App\Http\Controllers\Staff\PaymentTrackingController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\StaffApprovalController;
use App\Http\Controllers\Staff\AnnouncementController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\SelfRegistrationController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});
Route::get('session-registration', [SelfRegistrationController::class, 'index'])->name('self.registration');
Route::post('session-registration', [SelfRegistrationController::class, 'store'])->name('self.registration.store');
Route::post('membership-renewal', [SelfRegistrationController::class, 'renew'])->name('self.membership.renew');

Route::post('/attendance/timeout', [AttendanceController::class, 'timeOut'])->name('attendance.timeout');



Route::get('/landingProfile', [SelfRegistrationController::class, 'landingProfile'])
    ->middleware('approved.user')  // Apply the middleware
    ->name('self.landingProfile');

Route::get('/landing', function () {
    return view('self.landing');
})->name('self.landing');

// routes/web.php
Route::get('/waiting', [SelfRegistrationController::class, 'waiting'])->name('self.waiting');

// routes/web.php
Route::get('/check-approval', [SelfRegistrationController::class, 'checkApproval'])->name('self.checkApproval');


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

        Route::post('/attendance/timeout', [AttendanceController::class, 'timeout'])->name('attendance.timeout');
        // In web.php or api.php
        Route::post('/attendance/timeout', [AttendanceController::class, 'timeout']);


        Route::get('/staff/manage-approval', [StaffApprovalController::class, 'index'])->name('staff.manageApproval');
        Route::put('/staff/approve/{id}', [StaffApprovalController::class, 'approveUser'])->name('staff.approveUser');
        // Route::put('/staff/approve/{id}', [StaffApprovalController::class, 'approveUser'])->name('staff.renewMembership');
        // Define the route for rejecting users - using POST method
        Route::post('/staff/reject/{id}', [StaffApprovalController::class, 'rejectUser'])->name('staff.rejectUser');

        // If you're still having issues, add a debugging route to see all registered routes
        Route::get('/debug/routes', function () {
            $routes = collect(Route::getRoutes())->map(function ($route) {
                return [
                    'uri' => $route->uri(),
                    'name' => $route->getName(),
                    'methods' => $route->methods(),
                    'action' => $route->getActionName(),
                ];
            });
            
            return response()->json($routes);
        });


        // Route to record attendance
        Route::post('/staff/record-attendance', [AttendanceController::class, 'recordAttendance'])
            ->name('staff.record-attendance');

        Route::get('/viewmembers', [ViewmembersController::class, 'index'])->name('staff.viewmembers');
        Route::post('/staff/members/revoke', [ViewmembersController::class, 'revokeMember'])->name('revoke.membership');
        Route::post('/staff/members/restore', [ViewmembersController::class, 'restoreMember'])->name('restore.membership');
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

    // // **📩 Email Verification Routes**
    // Route::get('/email/verify', function () {
    //     return view('auth.verify-email');
    // })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/profile')->with('status', 'Email verified successfully!');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // // Member routes
    // Route::prefix('member')->group(function () {
    //     Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('members.dashboard');
    // });
    


    Route::get('/payment/success', function () {
        return view('payment.success');
    })->name('payment.success');
    
    Route::get('/payment/failed', function () {
        return view('payment.failed');
    })->name('payment.failed');


    Route::post('/logout-custom', [SelfRegistrationController::class, 'logout'])->name('logout.custom');



    Route::resource('announcements', \App\Http\Controllers\Staff\AnnouncementController::class);
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    

});

// Authentication routes (login, register, etc.)
require __DIR__.'/auth.php';
