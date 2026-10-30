<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($user->role === 'Super Admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('messages.index');
        }
    }
}