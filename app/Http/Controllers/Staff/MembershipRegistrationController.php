<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Hash;

class MembershipRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        $prices = [
            'session' => (object)['amount' => 60],
            'weekly' => (object)['amount' => 300],
            'monthly' => (object)['amount' => 850],
            'annual' => (object)['amount' => 10000],
        ];
        $today = Carbon::today()->toDateString();
        $maxBirthdate = Carbon::today()->subYears(16)->toDateString();

        return view('membership.registration', compact('prices', 'today', 'maxBirthdate'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today|after:'.Carbon::today()->subYears(100)->toDateString(),
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|digits:11',
            'email' => 'required|email|unique:users,email',
            'membership_type' => 'required|in:custom,7,30,365',
            'custom_days' => 'required_if:membership_type,custom|integer|min:1|max:365',
            'start_date' => 'required|date|after_or_equal:today',
            'uid' => 'required|string|max:255',
            'generated_password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            // Check if RFID UID exists and is unregistered
            $rfidTag = RfidTag::where('uid', $request->uid)->where('registered', 0)->first();
            if (!$rfidTag) {
                return redirect()->back()->withInput()->with('error', 'Invalid or already registered RFID UID.');
            }

            // Check if UID is already assigned to a user
            $existingUser = DB::table('users')->where('rfid_uid', $request->uid)->first();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'RFID UID is already assigned to another user.');
            }

            // Calculate expiry date
            $startDate = Carbon::parse($request->start_date);
            $duration = $request->membership_type === 'custom' ? $request->custom_days : $request->membership_type;
            $expiryDate = $startDate->copy()->addDays($duration - 1);

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'birthdate' => $request->birthdate,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'rfid_uid' => $request->uid,
                'password' => Hash::make($request->generated_password),
                'member_status' => 'active',
                'membership_type' => $request->membership_type,
                'start_date' => $startDate,
                'expiry_date' => $expiryDate,
            ]);

            // Update RFID tag to registered
            $rfidTag->update(['registered' => 1, 'user_id' => $user->id, 'updated_at' => Carbon::now('Asia/Manila')]);

            // Calculate payment amount
            $prices = [
                '7' => 300,
                '30' => 850,
                '365' => 10000,
                'custom' => 60 * ($request->membership_type === 'custom' ? $request->custom_days : 1),
            ];
            $paymentAmount = $prices[$request->membership_type] ?? 0;

            // Log payment (assuming you have a payments table)
            DB::table('payments')->insert([
                'user_id' => $user->id,
                'amount' => $paymentAmount,
                'payment_date' => Carbon::now('Asia/Manila'),
                'created_at' => Carbon::now('Asia/Manila'),
            ]);

            DB::commit();
            return redirect()->route('staff.membershipRegistration')->with('success', 'Member registered successfully!')->with('generated_password', $request->generated_password);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }
}