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
use App\Models\Price; // Import Price model

class MembershipRegistrationController extends Controller
{
    public function index()
    {
        // Fetch prices for the Blade template
        $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])->get()->keyBy('type');
        
        if (!$prices->has('session')) {
            return redirect()->route('staff.membershipRegistration')
                ->with('error', 'Session price not configured. Please contact the administrator.');
        }

        return view('staff.membershipRegistration', [
            'prices' => $prices,
            'maxBirthdate' => Carbon::today()->subYears(16)->format('Y-m-d'),
            'today' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
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
                'custom_days' => 'required_if:membership_type,custom|integer|min:1|max:365',
                'start_date' => 'required|date|after_or_equal:today',
                'birthdate' => ['required', 'date', 'before:today', 'before_or_equal:' . Carbon::today()->subYears(16)->format('Y-m-d')],
                'uid' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'rfid_uid')
                ],
                'generated_password' => 'required|string|min:8',
            ]);

            // Membership payment rates (fallback if Price model data is unavailable)
            $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])->get()->keyBy('type');
            $paymentRates = [
                '7' => $prices['weekly']->amount ?? 300,
                '30' => $prices['monthly']->amount ?? 800,
                '365' => $prices['annual']->amount ?? 2000,
                'custom' => $prices['session']->amount ?? 60,
            ];

            // Calculate payment amount
            if ($validatedData['membership_type'] === 'custom') {
                $days = $validatedData['custom_days'];
                $paymentAmount = $days * $paymentRates['custom'];
                $membershipDays = $days;
            } else {
                $paymentAmount = $paymentRates[$validatedData['membership_type']] ?? 0;
                $membershipDays = $validatedData['membership_type'];
            }

            // Use transaction for data consistency
            $user = DB::transaction(function () use ($validatedData, $paymentAmount, $membershipDays, $prices) {
                $priceType = match ($validatedData['membership_type']) {
                    'custom' => 'session',
                    '7' => 'weekly',
                    '30' => 'monthly',
                    '365' => 'annual',
                    default => throw new \Exception('Invalid membership type'),
                };

                $price = $prices[$priceType] ?? throw new \Exception("Price for {$priceType} not found");

                $user = User::create([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'gender' => $validatedData['gender'],
                    'phone_number' => $validatedData['phone_number'],
                    'birthdate' => $validatedData['birthdate'],
                    'password' => Hash::make($validatedData['generated_password']),
                    'role' => 'user',
                    'rfid_uid' => $validatedData['uid'],
                    'session_status' => 'approved', // Set to approved directly as in your original
                ]);

                // Create membership record
                $user->memberships()->create([
                    'price_id' => $price->id,
                    'amount_paid' => $paymentAmount,
                    'start_date' => $validatedData['start_date'],
                    'end_date' => Carbon::parse($validatedData['start_date'])
                        ->addDays((int)$membershipDays - 1)
                        ->format('Y-m-d'),
                    'status' => 'active',
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

                return $user;
            });

            return redirect()->route('staff.membershipRegistration')
                ->with([
                    'success' => 'Member registered successfully!',
                    'generated_password' => $validatedData['generated_password']
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