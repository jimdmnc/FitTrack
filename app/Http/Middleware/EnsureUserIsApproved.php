<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('self.registration'); // Redirect to registration if not logged in
        }

        // if (Auth::user()->session_status !== 'approved') {
        //     return redirect()->route('self.waiting'); // Redirect to waiting page if not approved
        // }

        return $next($request);
    }
}