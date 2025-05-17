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

    // Display the login form
    public function sessionLogin()
    {
        return view('self.login');
    }

    // Handle login submission
    public function loginSubmit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:8',
            ], [
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Please enter your password.',
                'password.min' => 'Password must be at least 8 characters.',
            ]);

            if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
                $user = Auth::user();

                // Check if the user is an admin trying to access userSession login
                if ($user->role === 'admin') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->back()->withInput()->with('error', 'Admins must login through the admin portal.');
                }

                if ($user->session_status === 'approved') {
                    return redirect()->route('self.landingProfile')->with('success', 'Login successful! Welcome back.');
                }

                return redirect()->route('self.waiting')->with('success', 'Login successful! Your session is pending approval.');
            }

            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', 'Login failed due to invalid input.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    // Check approval status via AJAX
    public function checkApproval()
    {
        $user = auth()->user();

        if ($user->session_status === 'approved') {
            $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
                ->whereNull('time_out')
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'rfid_uid' => $user->rfid_uid,
                    'attendance_date' => now(),
                    'time_in' => now(),
                    'status' => 'present',
                    'check_in_method' => 'auto',
                ]);
            }

            return response()->json(['approved' => true]);
        }

        if ($user->session_status === 'rejected') {
            return response()->json([
                'rejected' => true,
                'reason' => $user->rejection_reason ?? 'Your request could not be approved at this time.'
            ]);
        }

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
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'phone_number' => [
                    'required',
                    'digits:11',
                    'regex:/^09\d{9}$/'
                ],
                'email' => 'required|email|max:255|unique:users,email',
                'gender' => 'required|string|in:male,female,other',
                'membership_type' => 'required|string|in:1',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'first_name.regex' => 'First name cannot contain numbers or special characters.',
                'last_name.regex' => 'Last name cannot contain numbers or special characters.',
                'email.unique' => 'This email is already registered.',
                'phone_number.regex' => 'Phone number must be 11 digits starting with 09.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            $request->session()->forget('timed_out');

            $existingUser = User::where(function ($query) use ($validatedData) {
                $query->where('email', $validatedData['email'])
                      ->orWhere(function ($subQuery) use ($validatedData) {
                          $subQuery->where('first_name', $validatedData['first_name'])
                                   ->where('last_name', $validatedData['last_name']);
                      });
            })->first();

            if ($existingUser) {
                $existingUser->update([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'gender' => $validatedData['gender'],
                    'membership_type' => $validatedData['membership_type'],
                    'password' => Hash::make($validatedData['password']),
                    'session_status' => 'pending',
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now(),
                    'needs_approval' => true,
                ]);

                MembersPayment::create([
                    'rfid_uid' => $existingUser->rfid_uid,
                    'amount' => 60,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);

                Auth::login($existingUser);


                return redirect()->route('self.waiting')->with('success', 'Your session has been submitted for approval. Please wait for staff approval.');
            }

            $rfidUid = 'DAILY' . strtoupper(Str::random(5));

            $user = DB::transaction(function () use ($validatedData, $rfidUid) {
                $user = User::create([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'email' => $validatedData['email'],
                    'gender' => $validatedData['gender'],
                    'membership_type' => $validatedData['membership_type'],
                    'role' => 'userSession',
                    'session_status' => 'pending',
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now(),
                    'rfid_uid' => $rfidUid,
                    'password' => Hash::make($validatedData['password']),
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('self.registration')
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Registration failed due to invalid input.');
        } catch (\Exception $e) {
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

        $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        return view('self.landingProfile', compact('user', 'attendance'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('self.landing');
    }

    public function renew(Request $request)
    {
        try {
            $request->validate([
                'rfid_uid' => 'required|string',
                'membership_type' => 'required|string|in:1,3,6,12',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'amount' => 'required|numeric',
            ]);

            $request->session()->forget('timed_out');

            $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();

            $user->update([
                'membership_type' => $request->membership_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'session_status' => 'pending',
                'needs_approval' => true,
            ]);

            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $request->amount,
                'payment_date' => now(),
            ]);

            if (!Auth::check()) {
                Auth::login($user);
            }

            return redirect()->route('self.waiting')->with('success', 'Your membership renewal has been submitted for approval.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Renewal failed: ' . $e->getMessage());
        }
    }

    public function timeout(Request $request)
    {
        $request->validate(['rfid_uid' => 'required|string']);

        $attendance = Attendance::where('rfid_uid', $request->rfid_uid)
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        if ($attendance) {
            $attendance->update([
                'time_out' => now(),
                'status' => 'completed',
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No active session found']);
    }
}