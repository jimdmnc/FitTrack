<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Models\MembersPayment;

use Illuminate\Http\Request;

class PaymentTrackingController extends Controller
{
   // Display all members' payments
   public function index()
   {
       $payments = MembersPayment::with('user')->get(); // Fetch all payments with user details
       return view('staff.paymentTracking', compact('payments'));
   }

   // Store a new member payment
   public function store(Request $request)
   {
       $request->validate([
           'rfid_uid' => 'required|string|exists:users,rfid_uid', // Ensure RFID exists in users table
           'amount' => 'required|numeric',
           'payment_method' => 'required|string',
           'payment_date' => 'required|date',
       ]);

       MembersPayment::create([
           'rfid_uid' => $request->rfid_uid,
           'amount' => $request->amount,
           'payment_method' => $request->payment_method,
           'payment_date' => $request->payment_date,
       ]);

       return redirect()->route('staff.paymentTracking')->with('success', 'Payment added successfully.');
   }

   // Update an existing member payment
   public function update(Request $request, $id)
   {
       $payment = MembersPayment::findOrFail($id);
       
       $request->validate([
           'amount' => 'required|numeric',
           'payment_method' => 'required|string',
           'payment_date' => 'required|date',
       ]);

       $payment->update([
           'amount' => $request->amount,
           'payment_method' => $request->payment_method,
           'payment_date' => $request->payment_date,
       ]);

       return redirect()->route('staff.paymentTracking')->with('success', 'Payment updated successfully.');
   }

   // Delete a member's payment
   public function destroy($id)
   {
       MembersPayment::findOrFail($id)->delete();
       return redirect()->route('staff.paymentTracking')->with('success', 'Payment deleted successfully.');
   }
}