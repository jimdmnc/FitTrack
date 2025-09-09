<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Renewal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StaffApprovalController extends Controller
{
    // Show pending users (server-side rendering, kept for fallback)
    public function index()
    {
        $pendingUsers = User::where('session_status', 'pending')
            ->where('needs_approval', true)
            ->where(function ($query) {
                $query->where('role', 'user')
                    ->orWhere('role', 'userSession');
            })
            ->with(['renewal' => function ($query) {
                $query->where('status', 'pending')->latest();
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
                ->where(function ($query) {
                    $query->where('role', 'user')
                        ->orWhere('role', 'userSession');
                })
                ->with(['renewal' => function ($query) {
                    $query->where('status', 'pending')->latest();
                }]);

            // Apply filters
            $filter = $request->query('filter', 'all');
            if ($filter === 'today') {
                $query->whereDate('updated_at', today());
            } elseif ($filter === 'week') {
                $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }

            $pendingUsers = $query->get()->map(function ($user) {
                $renewal = $user->renewal;
                $payment_screenshot = $renewal && $renewal->payment_screenshot
                    ? (str_starts_with($renewal->payment_screenshot, 'http') 
                        ? $renewal->payment_screenshot 
                        : Storage::url($renewal->payment_screenshot))
                    : Storage::url('uploads/payments/default.png');

                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'gender' => ucfirst($user->gender),
                    'membership_type' => $renewal ? $renewal->membership_type : $user->membership_type,
                    'payment_method' => $renewal ? $renewal->payment_method : null,
                    'payment_screenshot' => $payment_screenshot,
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
            Log::error('Error fetching pending users: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending users: ' . $e->getMessage()
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
        try {
            $user = User::findOrFail($id);
            $user->member_status = 'active';
            $user->session_status = 'approved';
            $user->needs_approval = false;
            $user->save();

            $renewal = Renewal::where('rfid_uid', $user->rfid_uid)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($renewal) {
                $renewal->status = 'approved';
                $renewal->save();
            }

            $attendanceData = [
                'rfid_uid' => $user->rfid_uid,
                'status' => 'present',
                'attendance_date' => now()->toDateString(),
                'check_in_method' => 'manual',
                'session_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Add time_in for non-staff users
            if (!str_starts_with($user->rfid_uid, 'STAFF')) {
                $attendanceData['time_in'] = now();
            }

            DB::table('attendances')->insert($attendanceData);

            return redirect()->route('staff.manageApproval')->with('success', 'User approved and attendance recorded successfully!');
        } catch (\Exception $e) {
            Log::error('Error approving user: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error approving user: ' . $e->getMessage());
        }
    }

    public function rejectUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->session_status = 'rejected';
            $user->rejection_reason = $request->input('rejection_reason');
            $user->save();

            $renewal = Renewal::where('rfid_uid', $user->rfid_uid)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($renewal) {
                $renewal->status = 'rejected';
                $renewal->rejection_reason = $request->input('rejection_reason');
                $renewal->save();
            }

            return redirect()->route('staff.manageApproval')->with('success', 'Membership request rejected');
        } catch (\Exception $e) {
            Log::error('Error rejecting user: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error rejecting user: ' . $e->getMessage());
        }
    }
}