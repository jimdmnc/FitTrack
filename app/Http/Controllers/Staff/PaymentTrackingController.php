<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Models\MembersPayment;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PaymentTrackingController extends Controller
{
   // Display all members' payments
   public function index(Request $request)
{
    $query = MembersPayment::with('user')->orderBy('payment_date', 'desc');

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%")
              ->orWhere('rfid_uid', 'like', "%$search%");
        });
    }

    // Filter by payment method
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    // Filter by time range
    if ($request->filled('time_filter')) {
        $today = Carbon::today();

        switch ($request->time_filter) {
            case 'today':
                $query->whereDate('payment_date', $today);
                break;
            case 'week':
                $query->whereBetween('payment_date', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('payment_date', [
                    $today->copy()->startOfMonth(),
                    $today->copy()->endOfMonth()
                ]);
                break;
        }
    }

    // Fetch the filtered results with pagination
    $payments = $query->paginate(10);

    // If it's an AJAX request, return the entire table section
    if ($request->ajax()) {
        return view('staff.paymentTracking', compact('payments'))->render();
    }

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



   public function getPaymentHistory(Request $request)
   {
       $request->validate([
           'rfid_uid' => 'required|string|exists:users,rfid_uid'
       ]);
   
       $user = User::where('rfid_uid', $request->rfid_uid)->first();
       
       // Verify user has access to this RFID UID
       if (!$user->user()->where('rfid_uid', $request->rfid_uid)->exists()) {
           return response()->json([
               'success' => false,
               'message' => 'Unauthorized access to member data'
           ], 403);
       }
   
       $payments = MembersPayment::where('rfid_uid', $request->rfid_uid)
           ->orderBy('payment_date', 'desc')
           ->get()
           ->map(function ($payment) {
               return [
                   'id' => $payment->id,
                   'rfid_uid' => $payment->rfid_uid,
                   'amount' => (float) $payment->amount,
                   'payment_method' => $payment->payment_method,
                   'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
                   'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                   'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
               ];
           });
   
       return response()->json([
           'success' => true,
           'data' => $payments,
           'total_amount' => $payments->sum('amount'),
           'count' => $payments->count()
       ]);
   }

}