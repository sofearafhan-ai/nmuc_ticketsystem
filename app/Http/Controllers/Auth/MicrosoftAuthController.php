<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function handleMicrosoftCallback()
    {
        try {
            $msUser = Socialite::driver('microsoft')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Microsoft login failed. Please try again.');
        }

        $email = $msUser->getEmail();
        $name  = $msUser->getName() ?? explode('@', $email)[0];

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'nmuc_id'     => strtoupper(explode('@', $email)[0]),
                'full_name'   => $name,
                'email'       => $email,
                'password'    => Hash::make(Str::random(24)),
                'role'        => 'Authentication User',
                'user_status' => 'Active',
            ]);
        }

        Auth::login($user);

        if ($user->role === 'Super Admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('messages.index');
    }
}