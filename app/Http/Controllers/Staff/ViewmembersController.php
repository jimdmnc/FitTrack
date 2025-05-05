<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Renewal;
use App\Models\MembersPayment;
use Illuminate\Support\Facades\Log;
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
    try {
        // Get sort parameters from request, with defaults
        $sortColumn = $request->input('sort_column', 4); // Default to Registration Date
        $sortDirection = $request->input('sort_direction', -1); // Default to descending
        $searchQuery = $request->input('search');
        $status = $request->input('status', 'all');
        $page = $request->input('page', 1); // Get current page
        
        // Validate sortColumn to prevent errors
        $validColumns = [0, 1, 2, 3, 4, 5]; // Add all valid column indices
        if (!in_array($sortColumn, $validColumns)) {
            $sortColumn = 4; // Default if invalid
        }
        
        // Validate sortDirection to prevent errors
        if (!in_array($sortDirection, [1, -1])) {
            $sortDirection = -1; // Default if invalid
        }

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
        
        // Direction conversion from JavaScript to SQL
        $sqlDirection = $sortDirection > 0 ? 'asc' : 'desc';
        
        // Apply sorting based on the column index
        switch ($sortColumn) {
            case 0: // #
                $query->orderBy('id', $sqlDirection);
                break;
            case 1: // Name
                $query->orderBy('first_name', $sqlDirection)
                    ->orderBy('last_name', $sqlDirection);
                break;
            case 2: // Member ID
                $query->orderBy('rfid_uid', $sqlDirection);
                break;
            case 3: // Membership Type
                $query->orderBy('membership_type', $sqlDirection);
                break;
            case 4: // Registration Date
                $query->orderBy('start_date', $sqlDirection);
                break;
            case 5: // Status
                $query->orderBy('member_status', $sqlDirection);
                break;
            default:
                // Default to registration date descending
                $query->orderBy('start_date', 'desc');
        }
        
        // Ensure we preserve ALL current query parameters
        $members = $query->paginate(10)->appends([
            'sort_column' => $sortColumn,
            'sort_direction' => $sortDirection,
            'search' => $searchQuery,
            'status' => $status,
            'page' => $page
        ]);
        
        // For AJAX requests, return JSON response
        if ($request->ajax()) {
            try {
                // Make sure our view exists and can be rendered
                $tableView = view('partials.members_table', [
                    'members' => $members,
                    'sortColumn' => $sortColumn,
                    'sortDirection' => $sortDirection
                ])->render();
                
                // Create pagination HTML - FIX HERE: Using Bootstrap 4 pagination
                // Change from 'pagination.default' to 'vendor.pagination.bootstrap-4'
                // Or alternatively, use the simpler direct ->links() rendering
                $paginationView = $members->links()->render();
                
                // Return successful JSON response with all needed parameters
                return response()->json([
                    'table' => $tableView,
                    'pagination' => $paginationView,
                    'sortColumn' => $sortColumn,
                    'sortDirection' => $sortDirection,
                    'currentPage' => $members->currentPage() // Include current page in response
                ]);
            } catch (\Exception $e) {
                // Log the error for server logs
                Log::error('Error in members AJAX response: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
                // Return detailed error for debugging
                return response()->json([
                    'error' => 'Error rendering table: ' . $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
        }

        // For regular requests, return the view
        return view('staff.viewmembers', [
            'members' => $members,
            'query' => $searchQuery,
            'status' => $status,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection
        ]);
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error in members index: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        // Return an error view or redirect with a message
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
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