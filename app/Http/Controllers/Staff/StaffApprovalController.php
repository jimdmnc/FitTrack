<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment; // Add this if not already imported

class StaffApprovalController extends Controller
{
    // Show pending users
    public function index()
    {
        // Retrieve only users with session_status 'pending' and role 'user'
        $pendingUsers = User::where('session_status', 'pending')
            ->where('needs_approval', true)
            ->where('role', 'user')
            ->with(['payment' => function ($query) {
                $query->latest(); // Assuming you want the latest payment
            }])
            ->get();

        // Get the count of pending approvals
        $pendingApprovalCount = $pendingUsers->count();
        
        // Pass the count to the view
        return view('staff.manageApproval', compact('pendingUsers', 'pendingApprovalCount'));
    }

    // Approve New User Registration
    public function approveUser($id)
    {
        $user = User::findOrFail($id);

        // Update the user's session status to approved
        $user->member_status = 'active';
        $user->session_status = 'approved';
        $user->needs_approval = false;
        $user->save();

        // Create an attendance record when the user is approved
        DB::table('attendances')->insert([
            'rfid_uid' => $user->rfid_uid,
            'time_in' => now(),
            'status' => 'present',
            'attendance_date' => now()->toDateString(),
            'check_in_method' => 'manual',
            'session_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('staff.manageApproval')->with('success', 'User approved and attendance recorded successfully!');
    }

    // Reject User Registration
    public function rejectUser(Request $request, User $user)
{
    \Log::info('Rejecting user: ' . $user->id);
    \Log::info('Rejection reason: ' . $request->rejection_reason);
    // In your controller
    \Log::debug('Reject endpoint hit', ['user_id' => $user->id, 'input' => $request->all()]);
    
    // Validate the rejection reason
    $validated = $request->validate([
        'rejection_reason' => 'required|string|max:255',
    ]);

    // Update user status
    $user->update([
        'session_status' => 'rejected',
        'needs_approval' => false,
        'rejection_reason' => $request->rejection_reason
    ]);

    return redirect()->route('staff.manageApproval')
        ->with('success', 'Membership request rejected successfully.');
}
}