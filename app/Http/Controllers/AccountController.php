<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
                'confirmed',
                // Same rule as the Create Account page
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'password.confirmed' => 'The two new passwords do not match.',
            'password.min'       => 'Your new password must be at least 8 characters.',
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

        // Also replaces the "remember me" token, so other devices must log in again
        $user->forceFill([
            'password'       => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        RateLimiter::clear($rateLimitKey);

        $request->session()->regenerate();

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }
}