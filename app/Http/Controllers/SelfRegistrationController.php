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
use App\Models\Attendance;

class SelfRegistrationController extends Controller
{
    // Display the registration form for session membership
    public function index()
    {
        return view('self.registration');
    }

    // Check approval status via AJAX
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

    // Waiting approval page
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
                'phone_number' => [
                    'required',
                    'digits:11',
                    'regex:/^09\d{9}$/'
                ],
                'email' => 'required|email',
                'gender' => 'required|string|in:male,female,other',
                'membership_type' => 'required|string|in:1',
            ]);

            // Clear timed_out flag when registering new session
            $request->session()->forget('timed_out');

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
                    'end_date' => Carbon::now(),
                    'needs_approval' => true,
                ]);

                // Add a new payment record
                MembersPayment::create([
                    'rfid_uid' => $existingUser->rfid_uid,
                    'amount' => 60,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);

                Auth::login($existingUser);

                return redirect()->route('self.waiting')->with('success', 'Your session has been submitted for approval. Please wait for staff approval.');
            }

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
                    'end_date' => Carbon::now(),
                    'rfid_uid' => $rfidUid,
                    'password' => Hash::make('defaultpassword123'),
                    'needs_approval' => true,
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

        // Get the latest attendance record
        $attendance = DB::table('attendances')
            ->where('rfid_uid', $user->rfid_uid)
            ->whereDate('time_in', today())
            ->orderBy('time_in', 'desc')
            ->first();

        // Clear timed_out flag if user has a new attendance record with no time_out
        $attendance = Attendance::where('rfid_uid', auth()->user()->rfid_uid)
                ->whereNull('time_out')
                ->first();

        return view('self.landingProfile', compact('user', 'attendance'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to your landing page route
        return redirect()->route('self.landing');
    }

    public function renew(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'rfid_uid' => 'required|string',
                'membership_type' => 'required|string|in:1,3,6,12',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'amount' => 'required|numeric',
            ]);

            // Clear timed_out flag when renewing
            $request->session()->forget('timed_out');

            // Find the user
            $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

            // Update user membership
            $user->update([
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'session_status' => 'pending',
                'needs_approval' => true,
            ]);

            // Create payment record
            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $request->amount,
                'payment_date' => now(),
            ]);

            // Optionally log in the user if not already
            if (!Auth::check()) {
                Auth::login($user);
            }

            return redirect()->route('self.waiting')->with('success', 'Your membership renewal has been submitted for approval.');

        } catch (\Exception $e) {
            logger()->error('Membership Renewal Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Renewal failed: ' . $e->getMessage());
        }
    }
}