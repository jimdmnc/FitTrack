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
    // Retrieve users with session_status 'pending', needs_approval true, 
    // and either 'user' or 'userSession' role
    $pendingUsers = User::where('session_status', 'pending')
        ->where('needs_approval', true)
        ->where(function($query) {
            $query->where('role', 'user')
                  ->orWhere('role', 'userSession');
        })
        ->with(['payment' => function ($query) {
            $query->latest(); // Get the latest payment
        }])
        ->get();

    // Get the count of pending approvals
    $pendingApprovalCount = $pendingUsers->count();
    
    // Pass the data to the view
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

    public function rejectUser(Request $request, $id)
    {
        // Log the incoming request for debugging
        \Log::info('Reject User Request', [
            'id' => $id,
            'request_data' => $request->all(),
            'method' => $request->method()
        ]);

        try {
            $user = User::findOrFail($id);
            $user->session_status = 'rejected';
            $user->rejection_reason = $request->rejection_reason;
            $user->save();

            return redirect()->route('staff.manageApproval')->with('success', 'Membership request rejected');
        } catch (\Exception $e) {
            \Log::error('Error rejecting user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Error rejecting user: ' . $e->getMessage());
        }
    }

}