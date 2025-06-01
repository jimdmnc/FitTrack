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
use App\Models\Price;
use Illuminate\Support\Facades\Log;

class SelfRegistrationController extends Controller
{
    // Display the registration form for session membership
    public function index()
    {
        $announcements = Announcement::latest()->get();


        $sessionPrice = Price::where('type', 'session')->first();
        return view('self.registration', compact('sessionPrice', 'announcements'));
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

                // Handle pending users
                if ($user->session_status === 'pending') {
                    return redirect()->route('self.waiting')->with('info', 'Your session is pending approval. Please wait for staff approval.');
                }

                // Determine session status and expiration
                $message = 'Login successful! Welcome back.';
                $isExpired = $user->end_date && Carbon::parse($user->end_date)->isPast();

                if ($isExpired) {
                    $message = 'Login successful! Your session has expired. Please renew your membership below.';
                } elseif ($user->session_status === 'rejected') {
                    $message = 'Login successful! Your previous session was rejected: ' . ($user->rejection_reason ?? 'No reason provided') . '. You can renew your membership below.';
                }

                return redirect()->route('self.landingProfile')->with('success', $message);
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
            // Only automatically time in for userSession roles
            if ($user->role === 'userSession') {
                $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
                    ->whereNull('time_out')
                    ->whereDate('time_in', Carbon::today())
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
    public function viewAnnouncement()
    {
        $announcements = Announcement::latest()->get();
        return view('self.landingProfile', compact('announcements'));
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

            // Fetch session price
            $sessionPrice = Price::where('type', 'session')->first();
            if (!$sessionPrice) {
                throw new \Exception('Session price not configured.');
            }

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
                    'amount' => $sessionPrice->amount,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);

                Auth::login($existingUser);

                return redirect()->route('self.waiting')->with('success', 'Your session has been submitted for approval. Please wait for staff approval.');
            }

            $rfidUid = 'DAILY' . strtoupper(Str::random(5));

            $user = DB::transaction(function () use ($validatedData, $rfidUid, $sessionPrice) {
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
                    'amount' => $sessionPrice->amount,
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

    public function landing()
    {
        $user = Auth::user();
        $attendance = null;
        $timedOut = session('timed_out', false);

        if ($user && $user->rfid_uid) {
            $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->latest()
                ->first();

            $currentTime = Carbon::now();
            $autoCheckoutTime = Carbon::today()->setTime(21, 0, 0);

            if ($currentTime->greaterThan($autoCheckoutTime) && $attendance && $attendance->time_out) {
                $timedOut = true;
                session(['timed_out' => true]);
            }
        }

        return view('index', [
            'attendance' => $attendance,
            'timedOut' => $timedOut,
        ]);
    }

    public function landingProfile()
    {
        $user = Auth::user();
        $attendance = null;
        $timedOut = session('timed_out', false);
        $sessionPrice = null;
    
        if ($user && $user->rfid_uid) {
            $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->latest()
                ->first();
    
            // Auto time-in only for userSession roles
            if ($user->session_status === 'approved' && !$attendance && !$timedOut && $user->role === 'userSession') {
                $attendance = Attendance::create([
                    'rfid_uid' => $user->rfid_uid,
                    'attendance_date' => now(),
                    'time_in' => now(),
                    'status' => 'present',
                    'check_in_method' => 'auto',
                ]);
            }
            
            // Fetch session price
            $sessionPrice = Price::where('type', 'session')->first();
            if (!$sessionPrice) {
                throw new \Exception('Session price not configured.');
            }
    
            $currentTime = Carbon::now();
            $autoCheckoutTime = Carbon::today()->setTime(21, 0, 0);
    
            if ($currentTime->greaterThan($autoCheckoutTime)) {
                if ($attendance && !$attendance->time_out) {
                    // Auto time-out logic if needed
                    $attendance->update([
                        'time_out' => $autoCheckoutTime,
                        'status' => 'present'
                    ]);
                }
                $timedOut = true;
                session(['timed_out' => true]);
            }
        }
    
        return view('self.landingProfile', [
            'attendance' => $attendance,
            'timedOut' => $timedOut,
            'sessionPrice' => $sessionPrice ?? null,
        ]);
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
        Log::info('Renew Request:', $request->all());

        try {
            $validated = $request->validate([
                'rfid_uid' => 'required|string|exists:users,rfid_uid',
                'membership_type' => 'required|string|in:session', // Updated to accept 'session'
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'amount' => 'required|numeric|min:0',
            ]);

            $user = User::where('rfid_uid', $validated['rfid_uid'])->firstOrFail();

            // Check active attendance
            $activeAttendance = Attendance::where('rfid_uid', $validated['rfid_uid'])
                ->whereNull('time_out')
                ->first();

            if ($activeAttendance) {
                $message = 'Please time out before renewing your membership.';
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => $message], 422)
                    : redirect()->back()->with('error', $message);
            }

            $request->session()->forget('timed_out');

            DB::beginTransaction();
            $user->update([
                'membership_type' => $validated['membership_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'session_status' => 'pending',
                'needs_approval' => true,
            ]);

            MembersPayment::create([
                'rfid_uid' => $user->rfid_uid,
                'amount' => $validated['amount'],
                'payment_date' => now(),
            ]);

            DB::commit();

            if (!Auth::check()) {
                Auth::login($user);
            }

            $message = 'Your membership renewal has been submitted for approval.';
            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->route('self.waiting')->with('success', $message);
        } catch (ValidationException $e) {
            Log::warning('Validation failed for renew request:', ['errors' => $e->errors()]);
            return $request->ajax()
                ? response()->json(['success' => false, 'errors' => $e->errors()], 422)
                : redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Renewal error: ' . $e->getMessage(), ['exception' => $e]);
            $message = 'Renewal failed: ' . $e->getMessage();
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message)->withInput();
        }
    }

    public function timeout(Request $request)
    {
        \Log::info('Timeout request received for rfid_uid: ' . $request->rfid_uid);
        $request->validate(['rfid_uid' => 'required|string']);

        $attendance = Attendance::where('rfid_uid', $request->rfid_uid)
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        if ($attendance) {
            \Log::info('Updating attendance record: ' . json_encode($attendance));
            $attendance->update([
                'time_out' => now(),
                'status' => 'completed',
            ]);
            \Log::info('Attendance updated with time_out: ' . now());

            // Set session flag for UI
            $request->session()->put('timed_out', true);

            return response()->json(['success' => true]);
        }

        \Log::warning('No active session found for rfid_uid: ' . $request->rfid_uid);
        return response()->json(['success' => false, 'message' => 'No active session found']);
    }

    public function checkAttendanceStatus()
    {
        $user = Auth::user();
        $attendance = null;
        $timedOut = session('timed_out', false);

        if ($user && $user->rfid_uid) {
            $attendance = Attendance::where('rfid_uid', $user->rfid_uid)
                ->whereDate('time_in', Carbon::today())
                ->latest()
                ->first();

            $currentTime = Carbon::now();
            $autoCheckoutTime = Carbon::today()->setTime(21, 0, 0);

            if ($currentTime->greaterThan($autoCheckoutTime) && $attendance && $attendance->time_out) {
                $timedOut = true;
                session(['timed_out' => true]);
            }
        }

        return response()->json([
            'attendance' => $attendance ? [
                'time_in' => $attendance->time_in,
                'time_out' => $attendance->time_out,
            ] : null,
            'timedOut' => $timedOut,
        ]);
    }
}