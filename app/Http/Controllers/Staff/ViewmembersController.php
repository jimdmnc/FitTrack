<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Renewal;
use Illuminate\Http\Request;

class ViewmembersController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query
        $query = $request->input('search');
        // Get the filter status from the request
        $status = $request->input('status', 'all');
        // Fetch members with role 'user' and filter by name if a search query is provided
        $members = User::where('role', 'user')
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('member_status', $status);
            })
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4)
            ->withQueryString();

        $message = $members->isEmpty() ? 'No members found' : '';

        // Pass data to the view
        return view('staff.viewmembers', compact('members', 'query', 'message', 'status'));
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
        $user = User::where('rfid_uid', $request->rfid_uid)->firstOrFail();
        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }
        // Update user table
        
        $updated = $user->update([
            'membership_type' => $request->membership_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'member_status' => 'active', 
        ]);
        
        if (!$updated) {
            return response()->json(['message' => 'User update failed!'], 500);
        }
        
    
        // Save renewal history
        Renewal::create([
            'rfid_uid' => $user->rfid_uid,
            'membership_type' => $request->membership_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    
        // return response()->json(['message' => 'Membership renewed successfully']);

    }
    
}