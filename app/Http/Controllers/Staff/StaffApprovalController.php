<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffApprovalController extends Controller
{
    // Show pending users
    public function index()
    {
        // Retrieve only users with session_status 'pending' and role 'user'
        $pendingUsers = User::where('session_status', 'pending')
            ->where('needs_approval', true)
            ->where('role', 'user')
            ->get();

        return view('staff.manageApproval', compact('pendingUsers'));
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

    // Approve Membership Renewal
    // public function renewMembership($id)
    // {
    //     $user = User::findOrFail($id);

    //     // Update only the member_status
    //     $user->member_status = 'active';
    //     $user->needs_approval = false;
    //     $user->save();

    //     return redirect()->route('staff.manageApproval')->with('success', 'Membership renewed successfully!');
    // }

    // Reject User Request
    public function rejectUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->session_status = 'rejected';
        $user->rejection_reason = $request->rejection_reason;
        $user->save();

        return redirect()->route('staff.manageApproval')->with('success', 'User rejected successfully!');
    }
}
