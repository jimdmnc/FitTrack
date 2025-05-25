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
use App\Http\Controllers\SelfRegistrationController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\StaffController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/landing', function () {
    return view('self.landing');
})->name('self.landing');

// Self-registration routes
Route::get('session-registration', [SelfRegistrationController::class, 'index'])->name('self.registration');
Route::post('session-registration', [SelfRegistrationController::class, 'store'])->name('self.registration.store');
Route::post('membership-renewal', [SelfRegistrationController::class, 'renew'])->name('self.membership.renew');
Route::get('/attendance/status', [SelfRegistrationController::class, 'checkAttendanceStatus'])->name('self.checkAttendanceStatus');
Route::get('sessionLogin', [SelfRegistrationController::class, 'sessionLogin'])->name('self.login');
Route::post('sessionLogin', [SelfRegistrationController::class, 'loginSubmit'])->name('self.login.submit');

// RFID route (public)
Route::post('/rfid/attendance', [RFIDController::class, 'handleAttendance'])->name('rfid.attendance');

// Attendance timeout route (public, for compatibility with existing code)
Route::post('/attendance/timeout', [AttendanceController::class, 'timeOut'])->name('attendance.timeout');

// Routes requiring authentication
Route::middleware('auth')->group(function () {
    // Self-registration routes with approval middleware
    Route::middleware('approved.user')->group(function () {
        Route::get('/landingProfile', [SelfRegistrationController::class, 'landingProfile'])->name('self.landingProfile');
        Route::get('/waiting', [SelfRegistrationController::class, 'waiting'])->name('self.waiting');
        Route::get('/check-approval', [SelfRegistrationController::class, 'checkApproval'])->name('self.checkApproval');
    });

    // User-specific timeout route
    Route::post('/attendance/user-timeout', [SelfRegistrationController::class, 'timeout'])->name('attendance.timeout')->middleware('auth');

    // Logout route
    Route::post('/logout-custom', [SelfRegistrationController::class, 'logout'])->name('logout.custom');

    // Staff routes
    Route::prefix('staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
        Route::get('/peak-hours', [DashboardController::class, 'getPeakHours'])->name('staff.peakHours');

        // Membership Registration
        Route::get('/membershipRegistration', [MembershipRegistrationController::class, 'index'])->name('staff.membershipRegistration');
        Route::post('/membershipRegistration', [MembershipRegistrationController::class, 'store'])->name('staff.membershipRegistration.store');

        Route::post('/renew-membership', [ViewmembersController::class, 'renewMembership'])->name('renew.membership');

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('staff.attendance');
        Route::get('/staff/attendance', [AttendanceController::class, 'index'])->name('staff.attendance.index');
        Route::post('/record-attendance', [AttendanceController::class, 'recordAttendance'])->name('staff.record-attendance');

        // Staff Approval
        Route::get('/manage-approval', [StaffApprovalController::class, 'index'])->name('staff.manageApproval');
        Route::put('/approve/{id}', [StaffApprovalController::class, 'approveUser'])->name('staff.approveUser');
        Route::post('/reject/{id}', [StaffApprovalController::class, 'rejectUser'])->name('staff.rejectUser');
        Route::get('/staff/pending-users', [StaffApprovalController::class, 'getPendingUsers'])
            ->name('staff.pendingUsers')
            ->middleware('auth');

        // Debug routes
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
        })->name('debug.routes');

        // View Members
        Route::get('/viewmembers', [ViewmembersController::class, 'index'])->name('staff.viewmembers');
        Route::post('/members/revoke', [ViewmembersController::class, 'revokeMember'])->name('revoke.membership');
        Route::post('/members/restore', [ViewmembersController::class, 'restoreMember'])->name('restore.membership');
        Route::get('/membership-prices', [ViewmembersController::class, 'membershipPrices'])->name('staff.membership.prices');
        Route::post('/revoke-member', [ViewmembersController::class, 'revokeMember'])->name('revoke.member');
        Route::post('/restore-member', [ViewmembersController::class, 'restoreMember'])->name('restore.member');

        // Payment Tracking
        Route::get('/paymentTracking', [PaymentTrackingController::class, 'index'])->name('staff.paymentTracking');
        Route::post('/paymentTracking/store', [PaymentTrackingController::class, 'store'])->name('payments.store');
        Route::put('/paymentTracking/update/{id}', [PaymentTrackingController::class, 'update'])->name('payments.update');
        Route::delete('/paymentTracking/destroy/{id}', [PaymentTrackingController::class, 'destroy'])->name('payments.destroy');

        // Reports
        Route::get('/report', [ReportController::class, 'index'])->name('staff.report');
        Route::get('/generate-report', [ReportController::class, 'generateReport'])->name('generate.report');

        // Staff Management
        Route::get('/manage-staffs', [StaffController::class, 'manageStaffs'])->name('staff.manageStaffs');
        Route::get('/create-staff', [StaffController::class, 'createStaff'])->name('staff.createStaff');
        Route::post('/store-staff', [StaffController::class, 'storeStaff'])->name('staff.storeStaff');
        Route::get('/edit-staff/{id}', [StaffController::class, 'editStaff'])->name('staff.editStaff');
        Route::put('/update-staff/{id}', [StaffController::class, 'updateStaff'])->name('staff.updateStaff');
        Route::delete('/delete-staff/{id}', [StaffController::class, 'deleteStaff'])->name('staff.deleteStaff');
        Route::get('/staff/pending-approval-count', [StaffApprovalController::class, 'getPendingApprovalCount'])
            ->name('staff.pendingApprovalCount');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Price list routes
    Route::get('/profile/pricelist', [PriceController::class, 'pricelist'])->name('profile.pricelist');
    Route::post('/profile/pricelist/update', [PriceController::class, 'update'])->name('profile.pricelist.update');

    // Email Verification Routes
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/profile')->with('status', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

    // Payment routes
    Route::get('/payment/success', function () {
        return view('payment.success');
    })->name('payment.success');

    Route::get('/payment/failed', function () {
        return view('payment.failed');
    })->name('payment.failed');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    Route::get('/landing', [AnnouncementController::class, 'landing'])->name('landing');
});

// Authentication routes (login, register, etc.)
require __DIR__.'/auth.php';