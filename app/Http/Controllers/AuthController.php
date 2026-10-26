<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Tukar role ke lowercase supaya tidak sensitif pada huruf besar/kecil (contoh: 'Admin', 'admin', 'Super Admin')
            $userRole = strtolower(trim($user->role ?? ''));

            if ($userRole === 'super admin' || $userRole === 'super_admin') {
                return redirect()->route('superadmin.dashboard')->with('success', 'Welcome Super Admin!');
            }

            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
            }

            return redirect()->route('tickets.index')->with('success', 'Welcome Back!');
        }

        return back()->withErrors([
            'email' => 'The email or password entered is incorrect.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have successfully logged out.');
    }
}