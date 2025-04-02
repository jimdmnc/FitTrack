<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembersPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'email' => 'required|email|unique:users,email',  // Validate the email
            'gender' => 'required|string|in:male,female,other',  // Ensure gender is required and valid
            'membership_type' => 'required|string|in:1',

        ]);

        // Generate a unique RFID UID (using UUID)
        $rfidUid = Str::uuid();  // Generates a unique RFID UID

        $paymentAmount = 60;  // Fixed payment amount for session membership

        DB::transaction(function () use ($validatedData, $paymentAmount, $rfidUid) {
            // Create the session member user
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone_number' => $validatedData['phone_number'],
                'email' => $validatedData['email'],
                'gender' => $validatedData['gender'],
                'membership_type' => $validatedData['membership_type'],
                'role' => 'user',
                'session_status' => 'active',
                'start_date' => Carbon::now(),
                'rfid_uid' => $rfidUid,  // Use the unique RFID UID here
            ]);

            // Create a payment record for the session membership
            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,  // Use the RFID UID created for the user
                'amount' => $paymentAmount,
                'payment_method' => 'cash',
                'payment_date' => now(),
            ]);
        });

        // Redirect back to the registration form with success message
        return redirect()->route('self.registration')
            ->with(['success' => 'Session membership registered successfully!']);
    } catch (\Exception $e) {
        // Log the error and return an error message
        logger()->error('Session Membership Registration Error: ' . $e->getMessage());
        return redirect()->route('self.registration')
            ->withInput()
            ->with('error', 'Registration failed: ' . $e->getMessage());
    }
}


    
}
