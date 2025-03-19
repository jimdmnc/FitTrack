<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Make sure to import the User model


class DashboardController extends Controller
{
    public function index()
        {
            // Fetch only users with role 'user'
            $members = User::where('role', 'user') // Exclude admins
                ->select('rfid_uid', 'first_name', 'last_name', 'membership_type', 'start_date', 'member_status')
                ->get();
        
            return view('staff.dashboard', compact('members'));
        }
    

}
