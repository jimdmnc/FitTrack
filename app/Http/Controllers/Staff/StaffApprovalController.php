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
                            ->where('role', 'user')  // Add the condition for 'role'
                            ->get();
    
        return view('staff.manageApproval', compact('pendingUsers'));
    }
    

    // Approve User
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
    
        // Update the user's session status to approved
        $user->session_status = 'approved';
        $user->save();
    
        // Create an attendance record when the user is approved
        DB::table('attendances')->insert([
            'rfid_uid' => $user->rfid_uid,
            'time_in' => now(), // Current timestamp as time_in
            'status' => 'present', // Assuming the user is present when approved
            'attendance_date' => now()->toDateString(), // Today's date
            'check_in_method' => 'manual', // Assuming manual check-in method, adjust as needed
            'session_id' => null, // Assuming no session ID for now, adjust if necessary
            'created_at' => now(), // Created at timestamp
            'updated_at' => now() // Updated at timestamp
        ]);
    
        return redirect()->route('staff.manageApproval')->with('success', 'User approved and attendance recorded successfully!');
    }
    

    // Reject User
    public function rejectUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->session_status = 'rejected';
        $user->rejection_reason = $request->rejection_reason;
        $user->save();

        return redirect()->route('staff.manageApproval')->with('success', 'User rejected successfully!');
    }


}
