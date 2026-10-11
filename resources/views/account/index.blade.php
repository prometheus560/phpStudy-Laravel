@extends('layouts.app')

@section('title', 'Account')

@section('content')

<style>
    .eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; background: transparent; color: #7a7f95; border-radius: 9px; cursor: pointer; display: grid; place-items: center; }
    .eye:hover { color: #fff; background: rgba(255,255,255,.06); }
    .eye svg { width: 18px; height: 18px; }
    .eye .off { display: none; }
    .eye.on .off { display: block; }
    .eye.on .open { display: none; }
    .meter { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-top: 10px; }
    .meter span { height: 4px; border-radius: 99px; background: #232334; transition: background .25s; }
    .meter[data-s="1"] span:nth-child(-n+1) { background: #f87171; }
    .meter[data-s="2"] span:nth-child(-n+2) { background: #fb923c; }
    .meter[data-s="3"] span:nth-child(-n+3) { background: #fbbf24; }
    .meter[data-s="4"] span { background: #4ade80; }
    .rules span { font-size: 12px; padding: 4px 10px; border-radius: 99px; border: 1px solid #262636; color: #8b90a5; transition: .2s; }
    .rules span.ok { color: #4ade80; border-color: rgba(74,222,128,.4); background: rgba(74,222,128,.08); }
    .hint { font-size: 12px; margin-top: 7px; min-height: 16px; color: #8b90a5; }
    .hint.ok { color: #4ade80; }
    .hint.bad { color: #f87171; }
    .btn-red { display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 20px; border-radius: 12px; border: 1px solid rgba(248,113,113,.4); background: rgba(248,113,113,.10); color: #fca5a5; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: .2s; }
    .btn-red:hover:not(:disabled) { background: rgba(248,113,113,.2); }
    .btn-red:disabled { opacity: .45; cursor: not-allowed; }
</style>

@php
    $user = auth()->user();
    $lock = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>';
    $eye  = '<svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg>';
    $userIc = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    $mailIc = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>';

    $size = fn ($b) => $b >= 1048576 ? number_format($b / 1048576, 1) . ' MB' : number_format($b / 1024, 1) . ' KB';
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="flex items-center gap-4 mb-8">
        <span class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white text-xl font-bold flex items-center justify-center shadow-lg shadow-blue-900/40">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </span>
        <div>
            <h1 class="page-title">Account</h1>
            <p class="text-gray-400 text-sm mt-1">Manage your account information and password.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-ok" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

        {{-- Account Information --}}
        <div class="glass p-6">
            <h2 class="text-lg font-bold text-white mb-1">Account information</h2>
            <p class="text-gray-400 text-sm mb-6">Change your name or email, then save.</p>

            <form method="POST" action="{{ route('account.profile.update') }}" id="profile-form">
                @csrf
                @method('PATCH')

                <div class="mb-5">
                    <label for="name" class="f-label">Name</label>
                    <div class="control">
                        {!! $userIc !!}
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                               maxlength="100" required autocomplete="name"
                               class="f-input has-icon">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="email" class="f-label">Email</label>
                    <div class="control">
                        {!! $mailIc !!}
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                               maxlength="100" required autocomplete="email"
                               class="f-input has-icon">
                    </div>
                </div>

                {{-- Shown only when the email is being changed --}}
                <div class="mb-6" id="email-confirm" style="display:none">
                    <label for="email_password" class="f-label">Your password</label>
                    <div class="control">
                        {!! $lock !!}
                        <input type="password" id="email_password" name="current_password" autocomplete="current-password"
                               class="f-input has-icon" style="padding-right:46px" disabled>
                        <button type="button" class="eye" data-eye="email_password" aria-label="Show password">{!! $eye !!}</button>
                    </div>
                    <p class="hint">For your safety, enter your password to change your email.</p>
                </div>

                <button type="submit" id="profile-btn" class="btn-primary w-full sm:w-auto">Save changes</button>
            </form>

            <div class="flex flex-col mt-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 py-4 border-t border-white/10 {{ $user->created_at ? 'border-b' : '' }}">
                    <span class="text-gray-400 font-semibold text-sm">User ID</span>
                    <span class="text-gray-200 sm:text-right">{{ $user->id }}</span>
                </div>

                @if ($user->created_at)
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 py-4">
                        <span class="text-gray-400 font-semibold text-sm">Member since</span>
                        <span class="text-gray-200 sm:text-right">{{ \Illuminate\Support\Carbon::parse($user->created_at)->format('F d, Y') }}</span>
                    </div>
                @endif
            </div>

            {{-- Summary --}}
            <div class="grid grid-cols-2 gap-3 mt-4">
                @foreach ([
                    [$stats['subjects'], 'Subjects'],
                    [$stats['done'] . ' / ' . $stats['tasks'], 'Tasks done'],
                    [$stats['notes'], 'Notes'],
                    [$stats['files'], $stats['bytes'] > 0 ? 'Files · ' . $size($stats['bytes']) : 'Files'],
                ] as [$value, $label])
                    <div class="rounded-xl border border-white/10 bg-white/[0.03] p-4">
                        <p class="text-2xl font-bold text-white">{{ $value }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Change Password --}}
        <div class="glass p-6">
            <h2 class="text-lg font-bold text-white mb-1">Change password</h2>
            <p class="text-gray-400 text-sm mb-6">Use a password you don't use anywhere else.</p>

            <form method="POST" action="{{ route('account.password.update') }}" id="password-form">
                @csrf

                <div class="mb-5">
                    <label for="current_password" class="f-label">Current password</label>
                    <div class="control">
                        {!! $lock !!}
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                               class="f-input has-icon" style="padding-right:46px">
                        <button type="button" class="eye" data-eye="current_password" aria-label="Show password">{!! $eye !!}</button>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="f-label">New password</label>
                    <div class="control">
                        {!! $lock !!}
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                               class="f-input has-icon" style="padding-right:46px">
                        <button type="button" class="eye" data-eye="password" aria-label="Show password">{!! $eye !!}</button>
                    </div>
                    <div class="meter" id="meter" data-s="0" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
                    <p class="hint" id="strength" aria-live="polite">Use at least 8 characters.</p>
                    <div class="rules flex flex-wrap gap-2 mt-2">
                        <span id="rLen">8+ characters</span>
                        <span id="rLet">A letter</span>
                        <span id="rNum">A number</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="f-label">Confirm new password</label>
                    <div class="control">
                        {!! $lock !!}
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                               class="f-input has-icon" style="padding-right:46px">
                        <button type="button" class="eye" data-eye="password_confirmation" aria-label="Show password">{!! $eye !!}</button>
                    </div>
                    <p class="hint" id="match" aria-live="polite"></p>
                </div>

                <button type="submit" id="save-btn" class="btn-primary w-full sm:w-auto">Change password</button>
            </form>
        </div>

    </div>

    {{-- Devices --}}
    <div class="glass p-6 mt-6">
        <h2 class="text-lg font-bold text-white mb-1">Devices</h2>
        <p class="text-gray-400 text-sm leading-relaxed mb-5">
            @if ($otherDevices === null)
                Lost a phone or used a shared computer? Sign out everywhere except this device.
            @elseif ($otherDevices === 0)
                You are only signed in on this device.
            @else
                You are also signed in on {{ $otherDevices }} other {{ $otherDevices == 1 ? 'device' : 'devices' }}.
            @endif
        </p>

        <form method="POST" action="{{ route('account.sessions.logout_others') }}" class="flex flex-wrap items-start gap-3">
            @csrf

            <div style="min-width:240px;flex:1;max-width:360px">
                <div class="control">
                    {!! $lock !!}
                    <input type="password" id="sessions_password" name="sessions_password" required autocomplete="current-password"
                           placeholder="Your password" class="f-input has-icon" style="padding-right:46px">
                    <button type="button" class="eye" data-eye="sessions_password" aria-label="Show password">{!! $eye !!}</button>
                </div>
                @error('sessions_password', 'sessions')
                    <p class="hint bad">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-ghost" style="height:48px">Sign out other devices</button>
        </form>
    </div>

    {{-- Security --}}
    <div class="glass p-6 mt-6">
        <h2 class="text-lg font-bold text-white mb-3">Security</h2>

        <p class="text-gray-400 text-sm leading-relaxed mb-2">
            Your password is stored securely using Laravel's password hashing system.
            Changing it also signs you out of your other devices.
        </p>

        <p class="text-gray-400 text-sm leading-relaxed">
            If you forget your password, you can use the
            <a href="{{ route('password.request') }}" class="text-blue-400 font-semibold hover:text-blue-300">password reset</a>
            option from the login page.
        </p>
    </div>

    {{-- Danger zone --}}
    <div class="glass p-6 mt-6" style="border-color:rgba(248,113,113,.3)">
        <h2 class="text-lg font-bold mb-1" style="color:#fca5a5">Delete account</h2>
        <p class="text-gray-400 text-sm leading-relaxed mb-5">
            This permanently deletes your account, {{ $stats['subjects'] }} {{ $stats['subjects'] == 1 ? 'subject' : 'subjects' }},
            {{ $stats['tasks'] }} {{ $stats['tasks'] == 1 ? 'task' : 'tasks' }},
            {{ $stats['notes'] }} {{ $stats['notes'] == 1 ? 'note' : 'notes' }} and
            {{ $stats['files'] }} uploaded {{ $stats['files'] == 1 ? 'file' : 'files' }}. It cannot be undone.
        </p>

        <button type="button" id="delete-open" class="btn-red">Delete my account</button>
    </div>

</div>

{{-- Delete confirmation --}}
<dialog id="delete-dialog" class="bg-transparent p-0 m-auto w-full max-w-md backdrop:bg-black/70">
    <form method="POST" action="{{ route('account.destroy') }}" id="delete-form" class="glass p-6" style="border-color:rgba(248,113,113,.3)">
        @csrf
        @method('DELETE')

        <h3 class="text-lg font-bold mb-2" style="color:#fca5a5">Delete your account?</h3>
        <p class="text-gray-400 text-sm leading-relaxed mb-5">
            Everything you saved will be removed for good. Enter your password to confirm.
        </p>

        <div class="mb-4">
            <div class="control">
                {!! $lock !!}
                <input type="password" id="delete_password" name="delete_password" required autocomplete="current-password"
                       placeholder="Your password" class="f-input has-icon" style="padding-right:46px">
                <button type="button" class="eye" data-eye="delete_password" aria-label="Show password">{!! $eye !!}</button>
            </div>
            @error('delete_password', 'deleteAccount')
                <p class="hint bad">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-start gap-2 text-sm text-gray-300 mb-6 cursor-pointer">
            <input type="checkbox" id="delete-check" class="mt-1" style="accent-color:#f87171">
            <span>I understand this cannot be undone.</span>
        </label>

        <div class="flex justify-end gap-2">
            <button type="button" id="delete-cancel" class="btn-ghost">Cancel</button>
            <button type="submit" id="delete-confirm" class="btn-red" disabled>Delete everything</button>
        </div>
    </form>
</dialog>

<script>
(() => {
    document.querySelectorAll('[data-eye]').forEach(b => b.addEventListener('click', () => {
        const input = document.getElementById(b.dataset.eye);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        b.classList.toggle('on', show);
        b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    }));

    const pw = document.getElementById('password');
    const cf = document.getElementById('password_confirmation');
    const meter = document.getElementById('meter');
    const st = document.getElementById('strength');
    const mt = document.getElementById('match');
    const labels = ['Use at least 8 characters.', 'Weak password', 'Fair password', 'Good password', 'Strong password'];

    function strength() {
        const v = pw.value, len = v.length >= 8, letter = /[A-Za-z]/.test(v), num = /[0-9]/.test(v);
        document.getElementById('rLen').classList.toggle('ok', len);
        document.getElementById('rLet').classList.toggle('ok', letter);
        document.getElementById('rNum').classList.toggle('ok', num);
        let s = 0;
        if (v.length) s = 1;
        if (len && letter && num) s = 2;
        if (s === 2 && /[a-z]/.test(v) && /[A-Z]/.test(v)) s = 3;
        if (s === 3 && (/[^A-Za-z0-9]/.test(v) || v.length >= 12)) s = 4;
        meter.dataset.s = s;
        st.textContent = labels[s];
    }

    function match() {
        if (!cf.value) { mt.textContent = ''; mt.className = 'hint'; return; }
        const ok = cf.value === pw.value;
        mt.textContent = ok ? 'Passwords match' : 'Passwords do not match';
        mt.className = 'hint ' + (ok ? 'ok' : 'bad');
    }

    pw.addEventListener('input', () => { strength(); match(); });
    cf.addEventListener('input', match);

    document.getElementById('password-form').addEventListener('submit', e => {
        if (pw.value !== cf.value) { e.preventDefault(); match(); cf.focus(); return; }
        const btn = document.getElementById('save-btn');
        btn.disabled = true;
        btn.textContent = 'Saving...';
    });

    // Profile form: ask for the password only when the email is being changed
    const emailInput = document.getElementById('email');
    const originalEmail = emailInput.defaultValue.trim().toLowerCase();
    const confirmBox = document.getElementById('email-confirm');
    const confirmInput = document.getElementById('email_password');

    function checkEmail() {
        const changed = emailInput.value.trim().toLowerCase() !== originalEmail;
        confirmBox.style.display = changed ? '' : 'none';
        confirmInput.disabled = !changed;
        confirmInput.required = changed;
    }
    emailInput.addEventListener('input', checkEmail);
    checkEmail();

    document.getElementById('profile-form').addEventListener('submit', () => {
        const btn = document.getElementById('profile-btn');
        btn.disabled = true;
        btn.textContent = 'Saving...';
    });

    // Delete account dialog
    const dlg = document.getElementById('delete-dialog');
    const check = document.getElementById('delete-check');
    const confirmBtn = document.getElementById('delete-confirm');

    document.getElementById('delete-open').addEventListener('click', () => dlg.showModal());
    document.getElementById('delete-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
    check.addEventListener('change', () => { confirmBtn.disabled = !check.checked; });

    @if ($errors->deleteAccount->any())
        dlg.showModal();
    @endif

    // Re-enable buttons if the user comes back with the Back button
    window.addEventListener('pageshow', () => {
        const a = document.getElementById('profile-btn');
        const b = document.getElementById('save-btn');
        a.disabled = false; a.textContent = 'Save changes';
        b.disabled = false; b.textContent = 'Change password';
    });
})();
</script>

@endsection