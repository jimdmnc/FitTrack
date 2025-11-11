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

    // Fetch pending users for AJAX
    public function getPendingUsers(Request $request)
        {
            try {
                $query = User::where('session_status', 'pending')
                    ->where('needs_approval', true)
                    ->where(function($query) {
                        $query->where('role', 'user')
                            ->orWhere('role', 'userSession');
                    })
                    ->with(['payment' => function ($query) {
                        $query->latest();
                    }]);

                // Apply filters
                $filter = $request->query('filter', 'all');
                if ($filter === 'today') {
                    $query->whereDate('updated_at', today());
                } elseif ($filter === 'week') {
                    $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                }

                $pendingUsers = $query->get()->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'gender' => ucfirst($user->gender),
                        'membership_type' => $user->membership_type,
                        'payment_method' => $user->payment ? $user->payment->payment_method : null,
                        'payment_screenshot' => $user->payment && $user->payment->payment_screenshot 
                            ? \Storage::url($user->payment->payment_screenshot) 
                            : null,
                        'updated_at' => [
                            'date' => $user->updated_at->format('M d, Y'),
                            'time' => $user->updated_at->format('h:i A')
                        ],
                        'approve_url' => route('staff.approveUser', $user->id),
                        'reject_url' => route('staff.rejectUser', $user->id)
                    ];
                });

                return response()->json([
                    'success' => true,
                    'users' => $pendingUsers
                ]);
            } catch (\Exception $e) {
                Log::error('Error fetching pending users: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch pending users'
                ], 500);
            }
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
        
            if (!$user->needs_approval || $user->session_status !== 'pending') {
                return redirect()->route('staff.manageApproval')
                    ->with('error', 'This user has no pending renewal.');
            }
        
            DB::beginTransaction();
        
            try {
                // FIX: Use exact ENUM values (not strings with quotes)
                $user->update([
                    'member_status'   => 'active',
                    'session_status'  => 'approved',    // ← This is correct (string matches ENUM)
                    'needs_approval'  => 0,
                    'role'            => 'userSession',
                    'updated_at'      => now(),
                ]);
        
                // Verify the pending payment
                $paymentUpdated = MembersPayment::where('rfid_uid', $user->rfid_uid)
                    ->where('status', 'pending')
                    ->latest()
                    ->update([
                        'status'      => 'verified',
                        'verified_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        'updated_at'  => now(),
                    ]);
        
                DB::commit();
        
                return redirect()->route('staff.manageApproval')
                    ->with('success', "Approved successfully! Payment now visible in reports.");
        
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Approve User Failed: ' . $e->getMessage(), [
                    'user_id' => $id,
                    'trace' => $e->getTraceAsString()
                ]);
        
                return redirect()->route('staff.manageApproval')
                    ->with('error', 'Approval failed. Check logs.');
            }
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
            $user->needs_approval = false;

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