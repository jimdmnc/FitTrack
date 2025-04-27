<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Renewal;
use App\Models\MembersPayment;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ViewmembersController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $status = $request->input('status', 'all');
        
        $query = User::where('role', 'user');
        

        // Check for expired members by comparing the end_date with today's date
         $currentDate = Carbon::now(); // Get today's date

         $members = $query->get(); // Get all members first
        foreach ($members as $member) {
            if ($member->end_date && Carbon::parse($member->end_date)->isPast()) {
                // Update member status to expired if the end date is in the past
                $member->update(['member_status' => 'expired']);
            }
        }

        $query = User::where('role', 'user');

        if ($status !== 'all') {
            $query->where('member_status', $status);
        }
        
        if ($searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('first_name', 'like', "%{$searchQuery}%")
                  ->orWhere('last_name', 'like', "%{$searchQuery}%")
                  ->orWhere('rfid_uid', 'like', "%{$searchQuery}%");
            });
        }
        
        $members = $query->orderBy('created_at', 'desc')
                          ->paginate(10)
                          ->appends(request()->except('page'));
        
        // For AJAX requests, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'table' => view('partials.members_table', compact('members'))->render(),
                'pagination' => $members->links()->render()
            ]);
        }

        return view('staff.viewmembers', [
            'members' => $members,
            'query' => $searchQuery,
            'status' => $status
        ]);
    }

    /**
     * Handle membership renewal.
     */
    public function renewMembership(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|exists:users,rfid_uid',
            'membership_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
    
        // Find user by RFID
        $user = User::where('rfid_uid', $request->rfid_uid)->first();
    
        if (!$user) {
            return redirect()->back()->with('error', 'User not found!');
        }
    
        // Update user table
        $updated = $user->update([
            'membership_type' => $request->membership_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'member_status' => 'active',
        ]);
    
        if (!$updated) {
            return redirect()->back()->with('error', 'User update failed!');
        }
    
        // Save renewal history
        Renewal::create([
            'rfid_uid' => $user->rfid_uid,
            'membership_type' => $request->membership_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    
        return redirect()->route('staff.viewmembers')
            ->with('success', 'Member renewal successfully!');    
        
        }





        public function renewMembershipApp(Request $request)
        {
            try {
                // Validate request with more specific error messages
                $validated = $request->validate([
                    'rfid_uid' => 'required|exists:users,rfid_uid',
                    'membership_type' => 'required|in:monthly,quarterly,yearly,custom', // Added possible values
                    'start_date' => 'required|date|date_format:Y-m-d',
                    'end_date' => 'required|date|date_format:Y-m-d|after:start_date',
                    'payment_method' => 'required|in:cash,gcash',
                    'amount' => 'required|numeric|min:100', // Assuming minimum amount is 100
                ], [
                    'rfid_uid.exists' => 'The provided RFID does not match any registered user',
                    'end_date.after' => 'End date must be after start date',
                ]);
        
                // Find user - firstOrFail() already throws exception if not found
                $user = User::where('rfid_uid', $validated['rfid_uid'])->firstOrFail();
        
                $isCash = $validated['payment_method'] === 'cash';
                $sessionStatus = $isCash ? 'pending' : 'approved';
                $needsApproval = $isCash ? 1 : 0;
        
                // Use database transaction for atomic operations
                DB::beginTransaction();
        
                try {
                    // Update user membership
                    $updateData = [
                        'membership_type' => $validated['membership_type'],
                        'session_status' => $sessionStatus,
                        'needs_approval' => $needsApproval,
                    ];
        
                    if (!$isCash) {
                        // GCash payment - immediate activation
                        $updateData = array_merge($updateData, [
                            'start_date' => $validated['start_date'],
                            'end_date' => $validated['end_date'],
                            'member_status' => 'active',
                        ]);
                    } else {
                        // Cash payment - requires approval
                        $updateData['member_status'] = 'expired';
                    }
        
                    $user->update($updateData);
        
                    // Create Renewal record
                    $renewal = Renewal::create([
                        'rfid_uid' => $user->rfid_uid,
                        'membership_type' => $validated['membership_type'],
                        'start_date' => $validated['start_date'],
                        'end_date' => $validated['end_date'],
                        'payment_method' => $validated['payment_method'],
                        'status' => $sessionStatus,
                        'amount_paid' => $validated['amount'], // Added amount to renewal
                    ]);
        
                    // Create Payment record
                    MembersPayment::create([
                        'rfid_uid' => $user->rfid_uid,
                        'renewal_id' => $renewal->id, // Link payment to renewal
                        'amount' => $validated['amount'],
                        'payment_method' => $validated['payment_method'],
                        'payment_date' => now(),
                        'status' => $sessionStatus,
                    ]);
        
                    DB::commit();
        
                    return response()->json([
                        'success' => true,
                        'message' => $isCash 
                            ? 'Renewal request submitted. Waiting for staff approval.'
                            : 'Membership renewed successfully!',
                        'user' => $user->fresh(), // Get refreshed user data
                        'renewal' => $renewal,
                    ]);
        
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e; // Let the outer catch handle it
                }
        
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            } catch (\Exception $e) {
                \Log::error('Renewal Error: '.$e->getMessage()."\n".$e->getTraceAsString());
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your request',
                    'error' => config('app.debug') ? $e->getMessage() : null // Only show in debug mode
                ], 500);
            }
        }
        



    
    
}