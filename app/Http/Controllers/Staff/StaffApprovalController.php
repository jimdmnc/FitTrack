<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffApprovalController extends Controller
{
    // Show pending users (server-side rendering, kept for fallback)
    public function index()
    {
        $pendingUsers = User::where('session_status', 'pending')
            ->where('needs_approval', true)
            ->where(function($query) {
                $query->where('role', 'user')
                    ->orWhere('role', 'userSession');
            })
            ->with(['payment' => function ($query) {
                $query->latest();
            }])
            ->get();

        $pendingApprovalCount = $pendingUsers->count();
        
        return view('staff.manageApproval', compact('pendingUsers', 'pendingApprovalCount'));
    }

    public function pendingUsers(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = Renewal::where('status', 'pending')
            ->join('users', 'renewals.rfid_uid', '=', 'users.rfid_uid');
    
        if ($filter === 'today') {
            $query->whereDate('renewals.created_at', Carbon::today());
        } elseif ($filter === 'week') {
            $query->whereBetween('renewals.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }
    
        $users = $query->get()->map(function ($renewal) {
            $user = $renewal->user;
            return [
                'id' => $renewal->id, // Use renewal ID for approval/rejection
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'membership_type' => $renewal->membership_type,
                'payment_method' => $renewal->payment_method,
                'payment_screenshot' => $renewal->payment_screenshot ? Storage::url($renewal->payment_screenshot) : null,
                'updated_at' => [
                    'date' => $renewal->updated_at->format('Y-m-d'),
                    'time' => $renewal->updated_at->format('H:i:s'),
                ],
                'approve_url' => route('staff.approveUser', $renewal->id),
            ];
        });
    
        return response()->json(['success' => true, 'users' => $users]);
    }

    public function getPendingApprovalCount()
    {
        try {
            $count = User::where('needs_approval', true)->count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error fetching pending approval count: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch count'], 500);
        }
    }
    
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->member_status = 'active';
        $user->session_status = 'approved';
        $user->needs_approval = false;
        $user->save();

        DB::table('attendances')->insert([
            'rfid_uid' => $user->rfid_uid,
            // 'time_in' => now(),
            'status' => 'present',
            'attendance_date' => now()->toDateString(),
            'check_in_method' => 'manual',
            'session_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('staff.manageApproval')->with('success', 'User approved and attendance recorded successfully!');
    }
    // public function approveUser($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->member_status = 'active';
    //     $user->session_status = 'approved';
    //     $user->needs_approval = false;
    //     $user->save();
    
    //     $attendanceData = [
    //         'rfid_uid' => $user->rfid_uid,
    //         'status' => 'present',
    //         'attendance_date' => now()->toDateString(),
    //         'check_in_method' => 'manual',
    //         'session_id' => null,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ];
    
    //     // Add time_in only if the user has session_status of 'approved'
    //     // (or adjust this condition based on your actual role check)
    //     if ($user->role === 'userSession') {
    //         $attendanceData['time_in'] = now();
    //     }
    //     DB::table('attendances')->insert($attendanceData);
    
    //     return redirect()->route('staff.manageApproval')->with('success', 'User approved and attendance recorded successfully!');
    // }
    public function rejectUser(Request $request, $id)
    {
        Log::info('Reject User Request', [
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
            Log::error('Error rejecting user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error rejecting user: ' . $e->getMessage());
        }
    }
}