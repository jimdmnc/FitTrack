<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembersPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Import Auth at the top

class SelfRegistrationController extends Controller
{
    // Display the registration form for session membership
    public function index()
    {
        return view('self.registration');
    }

// Store method for handling self-registration

public function store(Request $request)
{
    try {
        // Validate the necessary fields
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|string|in:male,female,other',
            'membership_type' => 'required|string|in:1',
        ]);

        // Generate a unique RFID UID (using UUID)
        $rfidUid = Str::uuid();

        // Create the user inside a database transaction
        $user = DB::transaction(function () use ($validatedData, $rfidUid) {
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
                'gender' => $validatedData['gender'],
                'membership_type' => $validatedData['membership_type'],
                'role' => 'user',
                'session_status' => 'pending',
                'start_date' => Carbon::now(),
                'rfid_uid' => $rfidUid,
                'password' => Hash::make('defaultpassword123'), // Set a default password
            ]);

            // Create a payment record
            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => 60,
                'payment_method' => 'cash',
                'payment_date' => now(),
            ]);

            return $user;
        });

        // **Automatically log in the newly registered user**
        Auth::login($user);

        return redirect()->route('self.landing')->with('success', 'Registration successful! Welcome to our gym.');
    } catch (\Exception $e) {
        logger()->error('Session Membership Registration Error: ' . $e->getMessage());
        return redirect()->route('self.registration')
            ->withInput()
            ->with('error', 'Registration failed: ' . $e->getMessage());
    }
}


    
}
