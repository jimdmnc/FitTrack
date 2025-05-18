<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Allow access to landingProfile for expired or rejected users
            if ($request->route()->named('self.landingProfile')) {
                if ($user->session_status === 'pending') {
                    return redirect()->route('self.waiting')->with('info', 'Your session is pending approval. Please wait for staff approval.');
                }
                return $next($request); // Allow approved, expired, or rejected
            }

            // For other routes, require approval
            if ($user->needs_approval == 0 || ($user->end_date && Carbon::parse($user->end_date)->isPast()) || $user->session_status === 'rejected') {
                return $next($request);
            }
        }

        return redirect()->route('self.waiting')->with('error', 'Your account is pending approval.');
    }
}