<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembersPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SelfRegistrationController extends Controller
{
    // Display the registration form for session membership
    public function index()
    {
        return view('self.registration');
    }
// SelfRegistrationController.php
public function checkApproval()
{
    $user = auth()->user();

    // Check if user is approved
    if ($user->session_status === 'approved') {
        return response()->json(['approved' => true]);
    }

    // Check if user is rejected
    if ($user->session_status === 'rejected') {
        return response()->json([
            'rejected' => true,
            'reason' => $user->rejection_reason // Pass rejection reason if available
        ]);
    }

    // Default case, still pending
    return response()->json(['approved' => false]);
}


        // SelfRegistrationController.php
        public function waiting()
        {
            if (auth()->user()->session_status === 'approved') {
                return redirect()->route('self.landingProfile');
            }
            return view('self.waiting');
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
                    'email' => 'required|email',
                    'gender' => 'required|string|in:male,female,other',
                    'membership_type' => 'required|string|in:1',
                ]);
                $request->session()->forget('timed_out');  // <-- ADD THIS LINE

                // Check if user exists by email and phone
                $existingUser = User::where(function ($query) use ($validatedData) {
                    $query->where('email', $validatedData['email'])
                        ->orWhere(function ($subQuery) use ($validatedData) {
                            $subQuery->where('first_name', $validatedData['first_name'])
                                    ->where('last_name', $validatedData['last_name']);
                        });
                })->first();
                

                if ($existingUser) {
                    // Update user details and reset session_status
                    $existingUser->update([
                        'first_name' => $validatedData['first_name'],
                        'last_name' => $validatedData['last_name'],
                        'gender' => $validatedData['gender'],
                        'membership_type' => $validatedData['membership_type'],
                        'session_status' => 'pending',
                        'start_date' => Carbon::now(),
                        'end_date' => Carbon::now(), // ✅ Add this
                        'needs_approval' => true, // <-- ADD THIS

                    ]);

                    // Add a new payment record
                    MembersPayment::create([
                        'rfid_uid' => $existingUser->rfid_uid,
                        'amount' => 60,
                        'payment_method' => 'cash',
                        'payment_date' => now(),
                    ]);

                    Auth::login($existingUser);

                    return redirect()->route('self.waiting')->with('success', 'Your session has been submitted for approval. Please wait for staff approval.');                }

            // If user doesn't exist, generate a new RFID UID
            $rfidUid = 'DAILY' . strtoupper(Str::random(5));

            // Create the user and payment inside a transaction
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
                    'end_date' => Carbon::now(), // ✅ Add this here
                    'rfid_uid' => $rfidUid,
                    'password' => Hash::make('defaultpassword123'),
                    'needs_approval' => true, // ✅ here too

                ]);

                MembersPayment::create([
                    'rfid_uid' => $user->rfid_uid,
                    'amount' => 60,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);

                return $user;
            });

            Auth::login($user);

            return redirect()->route('self.waiting')->with('success', 'Registration successful! Welcome to our gym.');
        } catch (\Exception $e) {
            logger()->error('Session Membership Registration Error: ' . $e->getMessage());
            return redirect()->route('self.registration')
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function landingProfile()
{
    $user = auth()->user();

    if ($user->session_status !== 'approved') {
        return redirect()->route('self.waiting')->with('error', 'Your profile is not yet approved.');
    }

    return view('self.landingProfile', compact('user'));
}


public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect to your landing page route
    return redirect()->route('self.landing'); // Make sure this route exists
}

}
