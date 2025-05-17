<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware(function ($request, $next) {
    //         if (Auth::user()->role !== 'super_admin') {
    //             abort(403, 'Unauthorized action.');
    //         }
    //         return $next($request);
    //     });
    // }

    public function manageStaffs(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = User::whereIn('role', ['admin', 'super_admin']);

        if ($filter === 'admin') {
            $query->where('role', 'admin');
        } elseif ($filter === 'super_admin') {
            $query->where('role', 'super_admin');
        } elseif ($filter === 'approved') {
            $query->where('session_status', 'approved');
        } elseif ($filter === 'rejected') {
            $query->where('session_status', 'rejected');
        }

        $staffs = $query->get();

        if ($request->ajax()) {
            return response()->json(['staffs' => $staffs]);
        }

        return view('staff.manage-staffs', compact('staffs'));
    }

    public function createStaff(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'form' => [
                    'first_name' => '',
                    'last_name' => '',
                    'gender' => '',
                    'phone_number' => '',
                    'email' => '',
                    'role' => 'admin',
                    'session_status' => 'approved',
                ],
            ]);
        }

        return redirect()->route('staff.manageStaffs');
    }

    public function storeStaff(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'phone_number' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,super_admin',
                'session_status' => 'required|in:approved,rejected',
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'session_status' => $validated['session_status'],
                'needs_approval' => 0,
                'membership_type' => 'staff',
                'start_date' => now()->toDateString(),
                'member_status' => $validated['session_status'] === 'approved' ? 'active' : 'revoked',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff created successfully.',
                'staff' => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function editStaff(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        if ($request->ajax()) {
            return response()->json([
                'staff' => [
                    'id' => $staff->id,
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'gender' => $staff->gender,
                    'phone_number' => $staff->phone_number,
                    'email' => $staff->email,
                    'role' => $staff->role,
                    'session_status' => $staff->session_status,
                ],
            ]);
        }

        return redirect()->route('staff.manageStaffs');
    }

    public function updateStaff(Request $request, $id)
    {
        try {
            $staff = User::findOrFail($id);

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'phone_number' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:admin,super_admin',
                'session_status' => 'required|in:approved,rejected',
            ]);

            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'session_status' => $validated['session_status'],
                'membership_type' => 'staff',
                'member_status' => $validated['session_status'] === 'approved' ? 'active' : 'revoked',
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $staff->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully.',
                'staff' => $staff,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function deleteStaff($id)
    {
        $staff = User::findOrFail($id);
        if ($staff->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }
        $staff->delete();
        return response()->json([
            'success' => true,
            'message' => 'Staff deleted successfully.',
        ]);
    }
}
?>