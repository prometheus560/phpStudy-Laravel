<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AccountController extends Controller
{
   
    public function index()
    {
        return view('account.index');
    }

   
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $rateLimitKey = 'change-password:' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withErrors([
                    'current_password' =>
                        "Too many password change attempts. Please try again in {$seconds} seconds.",
                ])
                ->withInput();
        }

   

        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        if (!Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            RateLimiter::hit($rateLimitKey, 60);

            return back()
                ->withErrors([
                    'current_password' =>
                        'The current password is incorrect.',
                ])
                ->withInput();
        }
        if (Hash::check(
            $validated['password'],
            $user->password
        )) {
            RateLimiter::hit($rateLimitKey, 60);

            return back()
                ->withErrors([
                    'password' =>
                        'Your new password must be different from your current password.',
                ])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        RateLimiter::clear($rateLimitKey);

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }
}