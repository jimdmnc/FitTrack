<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\RfidTag;
use Carbon\Carbon;
use App\Models\MembersPayment;
use App\Models\Price;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MembershipRegistrationController extends Controller
{
    public function index()
    {
        $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])->get()->keyBy('type');
        
        // Check if all required price types exist
        $requiredTypes = ['session', 'weekly', 'monthly', 'annual'];
        $missingTypes = array_diff($requiredTypes, array_keys($prices->toArray()));
        if (!empty($missingTypes)) {
            return redirect()->route('staff.membershipRegistration')
                ->with('error', 'Missing price configurations for: ' . implode(', ', $missingTypes) . '. Please contact the administrator.');
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
            $prices = Price::whereIn('type', ['session', 'weekly', 'monthly', 'annual'])->get()->keyBy('type');

            // Clear custom_days if membership_type is not custom
            $input = $request->all();
            if ($request->input('membership_type') !== 'custom') {
                $input['custom_days'] = null;
            }
            $modifiedRequest = new Request($input);

            $maxBirthdate = Carbon::today()->subYears(16)->format('Y-m-d');

            $validatedData = $modifiedRequest->validate([
                'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    Rule::unique('users')->whereNull('deleted_at')
                ],
                'gender' => 'required|string|in:male,female,other',
                'phone_number' => [
                    'required',
                    'digits:11',
                    'regex:/^09\d{9}$/',
                    Rule::unique('users')->whereNull('deleted_at')
                ],
                'membership_type' => 'required|string|in:custom,7,30,365',
                'custom_days' => 'nullable|integer|min:1|max:365|required_if:membership_type,custom',
                'start_date' => 'required|date|after_or_equal:today',
                'birthdate' => ['required', 'date', 'before:today', "before_or_equal:{$maxBirthdate}"],
                'uid' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'rfid_uid')->whereNull('deleted_at'),
                    Rule::exists('rfid_tags', 'uid')->where('registered', false)
                ],
                'generated_password' => 'required|string|min:8|max:255',
            ], [
                'uid.exists' => 'The selected RFID tag is either already registered or invalid.',
                'email.unique' => 'This email is already registered.',
                'phone_number.unique' => 'This phone number is already registered.',
                'uid.unique' => 'This RFID tag is already associated with another user.',
            ]);

            $priceType = match ($validatedData['membership_type']) {
                'custom' => 'session',
                '7' => 'weekly',
                '30' => 'monthly',
                '365' => 'annual',
                default => throw new \Exception('Invalid membership type'),
            };

            $price = $prices[$priceType] ?? throw new \Exception("Price for {$priceType} not found");

            $membershipDays = match ($validatedData['membership_type']) {
                'custom' => $validatedData['custom_days'],
                '7' => 7,
                '30' => 30,
                '365' => 365,
                default => throw new \Exception('Invalid membership type'),
            };

            $paymentAmount = match ($validatedData['membership_type']) {
                'custom' => $validatedData['custom_days'] * $price->amount,
                '7', '30', '365' => $price->amount,
                default => throw new \Exception('Invalid membership type'),
            };

            $user = DB::transaction(function () use ($validatedData, $paymentAmount, $price, $membershipDays) {
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
                    'session_status' => 'approved',
                ]);

                $membership = $user->memberships()->create([
                    'price_id' => $price->id,
                    'amount_paid' => $paymentAmount,
                    'start_date' => $validatedData['start_date'],
                    'end_date' => Carbon::parse($validatedData['start_date'])
                        ->addDays((int)$membershipDays - 1)
                        ->format('Y-m-d'),
                    'status' => 'active',
                ]);

                MembersPayment::create([
                    'rfid_uid' => $user->rfid_uid,
                    'amount' => $paymentAmount,
                    'payment_method' => 'cash',
                    'payment_date' => now(),
                    'membership_id' => $membership->id, // Link payment to membership
                ]);

                RfidTag::where('uid', $validatedData['uid'])->update(['registered' => true]);

                return $user;
            });

            return redirect()->route('staff.membershipRegistration')
                ->with([
                    'success' => 'Member registered successfully!',
                    'generated_password' => $validatedData['generated_password']
                ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error during member registration', [
                'errors' => $e->errors(),
                'input' => $request->except(['generated_password'])
            ]);
            return redirect()->route('staff.membershipRegistration')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Registration error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['generated_password'])
            ]);
            return redirect()->route('staff.membershipRegistration')
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function getLatestRfid(Request $request)
    {
        try {
            $latestRfid = RfidTag::where('registered', false)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestRfid) {
                return response()->json(['uid' => null], 200);
            }

            return response()->json(['uid' => $latestRfid->uid], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch latest RFID', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to fetch RFID'], 500);
        }
    }

 
}