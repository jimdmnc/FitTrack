<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\RfidTag;
use Carbon\Carbon;
use App\Models\MembersPayment;


class MembershipRegistrationController extends Controller
{
    // Display the membership registration form
    public function index()
    {
        return view('staff.membershipRegistration');
    }

    // Handle the form submission (Manual Registration)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'membership_type' => 'required|string',
            'start_date' => 'required|date',
            'birthdate' => 'required|date', // ✅ Added birthdate validation
            'uid' => 'required|string|max:255|unique:users,rfid_uid',

        ]);
     // ✅ Membership payment rates
     $paymentRates = [
        "1" => 100,   // 1-day session
        "7" => 500,   // 7-day weekly
        "30" => 1800, // 30-day monthly
        "365" => 20000 // 1-year membership
    ];

        // ✅ Determine the amount based on the membership type
        $paymentAmount = $paymentRates[$validatedData['membership_type']] ?? 0;


        // ✅ Generate password using last name and birthdate
        $lastName = strtolower($validatedData['last_name']); // Convert last name to lowercase
        $birthdate = Carbon::parse($validatedData['birthdate'])->format('mdY'); // Format: MMDDYYYY
        $generatedPassword = $lastName . $birthdate; // Combine last name with birthdate

            
        // ✅ Hash the generated password before saving
        $validatedData['password'] = Hash::make($generatedPassword); // 🔥 Important: Hash the password!
    
        // Set default role
        $validatedData['role'] = 'user';
    
        // Rename 'uid' to 'rfid_uid'
        $validatedData['rfid_uid'] = $validatedData['uid'];
        unset($validatedData['uid']);
    
        // Calculate end_date
        $duration = (int) $request->input('membership_type');
        $validatedData['end_date'] = Carbon::parse($validatedData['start_date'])->addDays($duration)->format('Y-m-d');
    
    // ✅ Save user in 'users' table
        $user = User::create($validatedData);

        // ✅ Set payment method to "cash" by default
        MembersPayment::create([
            'rfid_uid' => $user->rfid_uid, 
            'amount' => $paymentAmount,
            'payment_method' => 'cash', // ✅ Default to "cash"
            'payment_date' => now(),
        ]);

        // ✅ Update RFID Tag
        RfidTag::where('uid', $request->input('uid'))->update(['registered' => true]);
    
        return redirect()->route('staff.membershipRegistration')
            ->with('success', 'Member registered successfully!');
    }


    

}
