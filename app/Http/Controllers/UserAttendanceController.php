<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserAttendanceController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated users with 'userSession' role can access
        $this->middleware(['auth', 'role:userSession']);
    }

    public function userAttendance(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Load the user with their attendances (uses rfid_uid as defined in User model)
        $user->load('attendances');

        // Render the userAttendance view with user data
        return view('self.userAttendance', compact('user'));
    }
}