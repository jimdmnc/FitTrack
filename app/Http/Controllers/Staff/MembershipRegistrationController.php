<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\RfidTag;
use Carbon\Carbon;
use App\Models\MembersPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MembershipRegistrationController extends Controller
{
    // Display the membership registration form
    public function index()
    {
        return view('staff.membershipRegistration');
    }

    public function store(Request $request)
    {
        try {
            // First, check if the membership type is not custom
            // If it's not custom, we'll set custom_days to null to avoid validation errors
            $input = $request->all();
            if ($request->input('membership_type') !== 'custom') {
                $input['custom_days'] = null;
            }
            
            // Create a new request with the modified input
            $modifiedRequest = new Request($input);
            
            $validatedData = $modifiedRequest->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')
                ],
                'gender' => 'required|string|in:male,female,other',
                'phone_number' => [
                    'required',
                    'digits:11',
                    'regex:/^09\d{9}$/'
                ],
                'membership_type' => 'required|string|in:custom,7,30,365',
                'custom_days' => 'nullable|integer|min:1|required_if:membership_type,custom',
                'start_date' => 'required|date|after_or_equal:today',
                'birthdate' => 'required|date|before:today',
                'uid' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'rfid_uid')
                ],
            ]);
    
            // Membership payment rates
            $paymentRates = [
                "7" => 300,   // 7-day weekly
                "30" => 850, // 30-day monthly
                "365" => 10000 // 1-year membership
            ];
    
            // Calculate payment amount
            if ($validatedData['membership_type'] === 'custom') {
                $days = $validatedData['custom_days'];
                $paymentAmount = $days * 60; // 60 pesos per day
                $membershipDays = $days;
            } else {
                $paymentAmount = $paymentRates[$validatedData['membership_type']] ?? 0;
                $membershipDays = $validatedData['membership_type'];
            }
    
            // Generate password
            $lastName = strtolower($validatedData['last_name']);
            $birthdate = Carbon::parse($validatedData['birthdate'])->format('mdY');
            $generatedPassword = $lastName . $birthdate;
    
            // Use transaction for data consistency
            DB::transaction(function () use ($validatedData, $paymentAmount, $generatedPassword, $membershipDays) {
                // Create user with default session_status as 'pending'
                $user = User::create([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'gender' => $validatedData['gender'],
                    'phone_number' => $validatedData['phone_number'],
                    'membership_type' => $validatedData['membership_type'] === 'custom' 
                        ? $validatedData['custom_days'] 
                        : $validatedData['membership_type'],
                    'start_date' => $validatedData['start_date'],
                    'birthdate' => $validatedData['birthdate'],
                    'password' => Hash::make($generatedPassword),
                    'role' => 'user',
                    'rfid_uid' => $validatedData['uid'],
                    'session_status' => 'approved', // Set directly to approved
                    'end_date' => Carbon::parse($validatedData['start_date'])
                        ->addDays((int)$membershipDays)
                        ->format('Y-m-d'),
                ]);
    
                // Create payment record
                MembersPayment::create([
                    'rfid_uid' => $user->rfid_uid,
                    'amount' => $paymentAmount,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                ]);
    
                // Update RFID tag
                RfidTag::where('uid', $validatedData['uid'])->update(['registered' => true]);
            });
    
            return redirect()->route('staff.membershipRegistration')
                ->with([
                    'success' => 'Member registered successfully!',
                    'generated_password' => $generatedPassword
                ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('staff.membershipRegistration')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            logger()->error('Registration Error: ' . $e->getMessage());
            return redirect()->route('staff.membershipRegistration')
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
    
    
}