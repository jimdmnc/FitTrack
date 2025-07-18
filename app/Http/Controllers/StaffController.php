<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function manageStaffs(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = User::whereIn('role', ['admin', 'super_admin']);

        if ($filter === 'admin') {
            $query->where('role', 'admin');
        } elseif ($filter === 'super_admin') {
            $query->where('role', 'super_admin');
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
            ]);

            // Generate RFID UID starting with STAFF followed by 5 random alphanumeric characters
            $rfid_uid = 'STAFF' . strtoupper(Str::random(5));

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'session_status' => 'approved',
                'rfid_uid' => $rfid_uid, // Auto-generated RFID UID
                'needs_approval' => 0,
                'membership_type' => 'staff',
                'start_date' => now()->toDateString(),
                'member_status' => 'active',
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
            ]);

            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'membership_type' => 'staff',
                'member_status' => 'active', // Default value
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