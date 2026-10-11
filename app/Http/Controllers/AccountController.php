<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Schedule;
use App\Models\StudyFile;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    private const DONE = ['Completed', 'completed', 'Done', 'done'];

    public function index(Request $request)
    {
        $user = Auth::user();

        $tasks = Task::whereHas('subject', fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'subjects' => $user->subjects()->count(),
            'tasks'    => (clone $tasks)->count(),
            'done'     => (clone $tasks)->whereIn('status', self::DONE)->count(),
            'notes'    => $user->notes()->count(),
            'files'    => $user->studyFiles()->count(),
            'bytes'    => (int) $user->studyFiles()->sum('file_size'),
        ];

        // How many other devices are logged in (only possible with database sessions)
        $otherDevices = null;

        if (config('session.driver') === 'database') {
            try {
                $otherDevices = DB::table(config('session.table', 'sessions'))
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $request->session()->getId())
                    ->count();
            } catch (\Throwable $e) {
                $otherDevices = null;
            }
        }

        return view('account.index', compact('stats', 'otherDevices'));
    }

    // Edit name and email. Changing the email needs the current password.
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'current_password' => ['nullable', 'string'],
        ], [
            'name.required'  => 'Please enter your name.',
            'email.required' => 'Please enter your email.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'That email is already used by another account.',
        ]);

        $emailChanged = $validated['email'] !== Str::lower($user->email);

        if ($emailChanged) {
            if (blank($request->input('current_password'))) {
                return back()
                    ->withErrors(['current_password' => 'Enter your password to change your email.'])
                    ->withInput();
            }

            $error = $this->confirmPassword($request, 'email', $request->input('current_password'));

            if ($error) {
                return back()
                    ->withErrors(['current_password' => $error])
                    ->withInput();
            }
        }

        $user->update([
            'name'  => trim($validated['name']),
            'email' => $validated['email'],
        ]);

        return back()->with('success', 'Your information has been updated.');
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

    // Sign out every device except this one
    public function logoutOtherDevices(Request $request)
    {
        $request->validateWithBag('sessions', [
            'sessions_password' => ['required', 'string'],
        ], [
            'sessions_password.required' => 'Enter your password to continue.',
        ]);

        $error = $this->confirmPassword($request, 'sessions', $request->input('sessions_password'));

        if ($error) {
            return back()->withErrors(['sessions_password' => $error], 'sessions');
        }

        $user = $request->user();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        // New "remember me" token: old "keep me logged in" cookies stop working.
        // This device gets a fresh one so you stay logged in here.
        $user->setRememberToken(Str::random(60));
        $user->save();

        Auth::login($user, true);

        return back()->with('success', 'You have been signed out of your other devices.');
    }

    // Delete the account and everything that belongs to it
    public function destroy(Request $request)
    {
        $request->validateWithBag('deleteAccount', [
            'delete_password' => ['required', 'string'],
        ], [
            'delete_password.required' => 'Enter your password to delete your account.',
        ]);

        $error = $this->confirmPassword($request, 'delete', $request->input('delete_password'));

        if ($error) {
            return back()->withErrors(['delete_password' => $error], 'deleteAccount');
        }

        $user = $request->user();

        // Remember the uploaded files, to remove them from storage afterwards
        $paths = $user->studyFiles()->pluck('file_path')->all();

        // Log out first. Doing it after the delete could re-create the user row.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        DB::transaction(function () use ($user) {
            $subjectIds = Subject::where('user_id', $user->id)->pluck('id');

            Task::where('user_id', $user->id)->orWhereIn('subject_id', $subjectIds)->delete();
            Schedule::whereIn('subject_id', $subjectIds)->delete();
            Note::where('user_id', $user->id)->delete();
            StudyFile::where('user_id', $user->id)->delete();
            Subject::where('user_id', $user->id)->delete();

            if (method_exists($user, 'pushSubscriptions')
                && Schema::hasTable(config('webpush.table_name', 'push_subscriptions'))) {
                $user->pushSubscriptions()->delete();
            }

            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }

            $user->delete();
        });

        if (!empty($paths)) {
            Storage::disk('local')->delete($paths);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Your account and all its data have been deleted.');
    }

    /**
     * Checks the password with a limit of 5 wrong tries per minute.
     * Returns an error message, or null when the password is correct.
     */
    private function confirmPassword(Request $request, string $action, ?string $password): ?string
    {
        $user = $request->user();
        $key  = 'account-' . $action . ':' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return 'Too many attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.';
        }

        if (!Hash::check((string) $password, $user->password)) {
            RateLimiter::hit($key, 60);

            return 'The password is incorrect.';
        }

        RateLimiter::clear($key);

        return null;
    }
}