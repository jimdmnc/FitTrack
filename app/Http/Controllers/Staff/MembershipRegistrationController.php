<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MembershipRegistrationController extends Controller
{
    // Display the membership registration form
    public function index()
    {
        return view('staff.membershipRegistration');
    }

    // Handle the form submission
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
            'rfid_uid' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hash the password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Set default role to 'user'
        $validatedData['role'] = 'user';

        // Create the user
        User::create($validatedData);

        // Redirect with a success message
        return redirect()->route('staff.membershipRegistration')->with('success', 'Member registered successfully!');
    }
}