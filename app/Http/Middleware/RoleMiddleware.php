<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('/login')->withErrors(['error' => 'You must be logged in.']);
        }

        if (Auth::user()->role !== $role) {
            return redirect('/')->withErrors(['error' => 'Unauthorized access.']);
        }

        return $next($request);
    }
}
