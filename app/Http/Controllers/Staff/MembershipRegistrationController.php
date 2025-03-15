<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\RfidTag;

class MembershipRegistrationController extends Controller
{
    // Display the membership registration form
    public function index()
    {
        return view('staff.membershipRegistration');
    }

    // Handle the form submission (Manual Registration)
    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'membership_type' => 'required|string',
            'start_date' => 'required|date',
            'uid' => 'required|string|max:255|unique:users,rfid_uid', // Validate 'uid' but map to 'rfid_uid'
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hash the password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Set default role to 'user'
        $validatedData['role'] = 'user';

        // Rename 'uid' to 'rfid_uid' before saving to the database
        $validatedData['rfid_uid'] = $validatedData['uid'];
        unset($validatedData['uid']); // Remove the 'uid' key from the array

        // Create the user
        User::create($validatedData);

        // Delete the UID from the rfid_tags table
        RfidTag::where('uid', $request->input('uid'))->update(['registered' => true]);

        // Redirect with a success message
        return redirect()->route('staff.membershipRegistration')->with('success', 'Member registered successfully!');
    }



    

    // 📌 NEW: Register User via RFID API
    public function registerFromRFID(Request $request)
    {
        // Validate only RFID UID
        $validatedData = $request->validate([
            'rfid_uid' => 'required|string|max:255|unique:users',
        ]);

        // Generate default values (Modify if needed)
        $user = User::create([
            'first_name' => 'Auto',
            'last_name' => 'User',
            'email' => 'auto_user_' . time() . '@example.com',
            'gender' => 'Unspecified',
            'phone_number' => '0000000000',
            'membership_type' => 'Basic',
            'start_date' => now(),
            'rfid_uid' => $validatedData['rfid_uid'],
            'password' => Hash::make('defaultPassword123'),
            'role' => 'user'
        ]);

        return response()->json([
            'message' => 'Member registered via RFID!',
            'user' => $user
        ], 201);
    }
}
