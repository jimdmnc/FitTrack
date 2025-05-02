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
    /**
     * Check and update member status based on expiration date
     * 
     * @param User $member
     * @return void
     */
    private function updateMemberStatus(User $member)
    {
        $currentDate = Carbon::now();
        
        // Check if member has an end date and it's in the past
        if ($member->end_date && Carbon::parse($member->end_date)->lt($currentDate)) {
            // Only update if current status is not already expired or revoked
            if ($member->member_status !== 'expired' && $member->member_status !== 'revoked') {
                $member->member_status = 'expired';
                $member->save();
            }
        } elseif ($member->end_date && Carbon::parse($member->end_date)->gt($currentDate)) {
            // If end date is in the future and status is expired, set back to active
            if ($member->member_status === 'expired') {
                $member->member_status = 'active';
                $member->save();
            }
        }
    }

    public function index(Request $request)
    {
        $searchQuery = $request->input('search');
        $status = $request->input('status', 'all');
        
        // First update all members' status based on their end dates
        $allMembers = User::where('role', 'user')->get();
        foreach ($allMembers as $member) {
            $this->updateMemberStatus($member);
        }

        // Now build the query with filters
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
        
        // Sorting alphabetically by first_name and last_name
        $members = $query->orderBy('first_name', 'asc')  // Or 'desc' for reverse order
                        ->orderBy('last_name', 'asc')   // Sorting by last_name as well
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
  
    /**
     * Revoke a member's access
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revokeMember(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            // Find the member by RFID UID
            $member = User::where('rfid_uid', $request->rfid_uid)->first();
            
            if (!$member) {
                return redirect()->back()->with('error', 'Member not found.');
            }

            // Even if the member's status is expired, we should allow revocation
            $member->member_status = 'revoked';  // Update status to revoked
            $member->revoke_reason = $request->reason; // Set the revoke reason (if provided)
            $member->revoked_at = now(); // Set the date/time of revocation
            $member->save();  // Save the changes

            // Add a log entry if you have a logs table
            // ActivityLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'Revoked membership for ' . $member->first_name . ' ' . $member->last_name,
            //     'description' => 'Reason: ' . ($request->reason ?? 'No reason provided'),
            // ]);

            return redirect()->back()->with('success', 'Member has been revoked successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to revoke member: ' . $e->getMessage());
        }
    }

    /**
     * Restore a revoked member
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restoreMember(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required',
        ]);

        try {
            // Find the member by RFID UID
            $member = User::where('rfid_uid', $request->rfid_uid)->first();
            
            if (!$member) {
                return redirect()->back()->with('error', 'Member not found.');
            }

            // Check if the member was actually revoked
            if ($member->member_status !== 'revoked') {
                return redirect()->back()->with('error', 'This member is not revoked.');
            }

            // After restoring, check the end date to determine the correct status
            $currentDate = Carbon::now();
            
            if ($member->end_date && Carbon::parse($member->end_date)->lt($currentDate)) {
                $member->member_status = 'expired';
            } else {
                $member->member_status = 'active';
            }

            // Reset revoke reason and revoke date
            $member->revoke_reason = null;
            $member->revoked_at = null;
            $member->save();

            // Optionally add a log entry (if using activity logging)
            // ActivityLog::create([
            //     'user_id' => auth()->id(),
            //     'action' => 'Restored membership for ' . $member->first_name . ' ' . $member->last_name,
            //     'description' => 'Member was restored from revoked status',
            // ]);

            return redirect()->back()->with('success', 'Member has been restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore member: ' . $e->getMessage());
        }
    }
}