<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        // NEW: already logged in? Skip the login page.
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);


        $email = Str::lower(trim($credentials['email']));

        $rateLimitKey = 'login:' . $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {

            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withErrors([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ])
                ->onlyInput('email');
        }


        // The "true" keeps the user logged in even after the browser is closed.
        if (Auth::attempt(
            [
                'email' => $email,
                'password' => $credentials['password'],
            ],
            true
        )) {

            RateLimiter::clear($rateLimitKey);

            $request->session()->regenerate();


            return redirect()->intended(
                route('dashboard')
            );
        }

        RateLimiter::hit($rateLimitKey, 60);


        return back()
            ->withErrors([
                'email' => 'The email or password is incorrect.',
            ])
            ->onlyInput('email');
    }


    public function showRegister()
    {
        // NEW: already logged in? Skip the register page.
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        $user = User::create([
            'name' => $validated['name'],

            'email' => Str::lower(
                trim($validated['email'])
            ),

            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        Auth::login($user, true);

        $request->session()->regenerate();


        return redirect()->route('dashboard');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect()->route('login');
    }
}