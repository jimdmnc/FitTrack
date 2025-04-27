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








        
        



    
    
}