<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100'],
        ]);

        $email = strtolower(trim($validated['email']));

        $rateLimitKey = 'password-reset:' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withErrors([
                    'email' => "Too many requests. Please try again in {$seconds} seconds.",
                ])
                ->withInput();
        }

        RateLimiter::hit($rateLimitKey, 60);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()->with(
                'status',
                'If an account exists with that email, a password reset link has been sent.'
            );
        }

        $token = Str::random(64);

        Cache::put(
            'password-reset:' . hash('sha256', $token),
            [
                'user_id' => $user->id,
                'email' => $email,
            ],
            now()->addMinutes(30)
        );

        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(30),
            [
                'token' => $token,
                'email' => $email,
            ]
        );

        Mail::to($user->email)->send(
            new PasswordResetMail($user, $resetUrl)
        );

        return back()->with(
            'status',
            'If an account exists with that email, a password reset link has been sent.'
        );
    }

    public function showResetPassword(Request $request, string $token)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'This password reset link is invalid or has expired.');
        }

        $email = strtolower(trim($request->query('email', '')));

        if (!$email) {
            abort(403, 'Invalid password reset link.');
        }

        $resetData = Cache::get(
            'password-reset:' . hash('sha256', $token)
        );

        if (
            !$resetData ||
            $resetData['email'] !== $email
        ) {
            abort(403, 'This password reset link is invalid or has expired.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:100'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $email = strtolower(trim($validated['email']));

        $resetData = Cache::get(
            'password-reset:' . hash('sha256', $validated['token'])
        );

        if (
            !$resetData ||
            $resetData['email'] !== $email
        ) {
            return back()->withErrors([
                'email' => 'This password reset link is invalid or has expired.',
            ]);
        }

        $user = User::whereRaw(
            'LOWER(email) = ?',
            [$email]
        )->first();

        if (
            !$user ||
            $user->id !== $resetData['user_id']
        ) {
            return back()->withErrors([
                'email' => 'This password reset link is invalid or has expired.',
            ]);
        }

        $user->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        Cache::forget(
            'password-reset:' . hash(
                'sha256',
                $validated['token']
            )
        );

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Your password has been changed successfully. You can now log in.'
            );
    }
}