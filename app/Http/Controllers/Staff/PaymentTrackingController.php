<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Models\MembersPayment;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PaymentTrackingController extends Controller
{
   // Display all members' payments from renewals history
public function index(Request $request)
{
    $query = Renewal::with('user')
        ->orderBy('created_at', 'desc');
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%");
        });
    }

    // Filter by payment method
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    // Filter by membership type (session, week, month, annual)
    if ($request->filled('membership_type')) {
        $membership = strtolower($request->membership_type);
        $map = [
            'session' => 'custom',
            'week' => '7',
            'month' => '30',
            'annual' => '365',
        ];

        if (isset($map[$membership])) {
            $type = $map[$membership];
            $query->where('membership_type', $type);
        }
    }

    // Filter by time range
    if ($request->filled('time_filter')) {
        $today = Carbon::today();

        switch ($request->time_filter) {
            case 'today':
                $query->whereDate('created_at', $today);
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('created_at', [
                    $today->copy()->startOfMonth(),
                    $today->copy()->endOfMonth()
                ]);
                break;
            case 'custom':
                // Custom range: filter by created_at (when renewal was made)
                $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
                $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                } elseif ($start) {
                    $query->where('created_at', '>=', $start);
                } elseif ($end) {
                    $query->where('created_at', '<=', $end);
                }
                break;
        }
    }

    // Fetch the filtered results with pagination
    $payments = $query->paginate(10);

    if ($request->ajax()) {
        return view('staff.paymentTracking', compact('payments'));
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





}