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
    ->middleware('approved.user')
    ->name('self.landingProfile');

Route::get('/landing', function () {
    return view('self.landing');
})->name('self.landing');

Route::get('/waiting', [SelfRegistrationController::class, 'waiting'])->name('self.waiting');
Route::get('/check-approval', [SelfRegistrationController::class, 'checkApproval'])->name('self.checkApproval');

Route::post('/rfid/attendance', [RFIDController::class, 'handleAttendance']);

Route::middleware('auth')->group(function () {
    // Staff routes
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/peak-hours', [DashboardController::class, 'getPeakHours']);

        Route::get('/membershipRegistration', [MembershipRegistrationController::class, 'index'])->name('membershipRegistration');
        Route::post('/membershipRegistration', [MembershipRegistrationController::class, 'store'])->name('membershipRegistration.store');

        Route::post('/renew-membership', [ViewmembersController::class, 'renewMembership'])->name('renew.membership');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::post('/record-attendance', [AttendanceController::class, 'recordAttendance'])->name('record-attendance');

        Route::get('/manage-approval', [StaffApprovalController::class, 'index'])->name('manageApproval');
        Route::put('/approve/{id}', [StaffApprovalController::class, 'approveUser'])->name('approveUser');
        Route::post('/reject/{id}', [StaffApprovalController::class, 'rejectUser'])->name('rejectUser');

        Route::get('/viewmembers', [ViewmembersController::class, 'index'])->name('viewmembers');
        Route::post('/members/revoke', [ViewmembersController::class, 'revokeMember'])->name('revoke.membership');
        Route::post('/members/restore', [ViewmembersController::class, 'restoreMember'])->name('restore.membership');

        Route::get('/paymentTracking', [PaymentTrackingController::class, 'index'])->name('paymentTracking');
        Route::post('/paymentTracking/store', [PaymentTrackingController::class, 'store'])->name('payments.store');
        Route::put('/paymentTracking/update/{id}', [PaymentTrackingController::class, 'update'])->name('payments.update');
        Route::delete('/paymentTracking/destroy/{id}', [PaymentTrackingController::class, 'destroy'])->name('payments.destroy');

        Route::get('/report', [ReportController::class, 'index'])->name('report');
        Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('generate.report');
    });

    // Announcement routes
    Route::resource('announcements', AnnouncementController::class);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Email Verification Routes
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/profile')->with('status', 'Email verified successfully!');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // Member routes
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    });

    // Payment routes
    Route::get('/payment/success', function () {
        return view('payment.success');
    })->name('payment.success');
    Route::get('/payment/failed', function () {
        return view('payment.failed');
    })->name('payment.failed');

    Route::post('/logout-custom', [SelfRegistrationController::class, 'logout'])->name('logout.custom');
});

// Authentication routes
require __DIR__.'/auth.php';