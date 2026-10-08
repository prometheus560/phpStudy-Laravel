@extends('layouts.auth')

@section('title', 'Create account')

@section('content')

    <h1>Create your <span class="grad">account</span></h1>
    <p class="sub">It takes less than a minute.</p>

    @if ($errors->any())
        <div class="alert err" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}" data-busy="Creating account...">
        @csrf

        <div class="field">
            <label for="name">Full name</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your full name" maxlength="100" autocomplete="name" class="{{ $errors->has('name') ? 'bad' : '' }}" required autofocus>
            </div>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" maxlength="100" autocomplete="email" class="{{ $errors->has('email') ? 'bad' : '' }}" required>
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <input type="password" id="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" class="{{ $errors->has('password') ? 'bad' : '' }}" required>
                <button type="button" class="eye" data-eye="password" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
            </div>
            <div class="meter" id="meter" data-s="0" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
            <p class="hint" id="strength" aria-live="polite"></p>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" autocomplete="new-password" required>
                <button type="button" class="eye" data-eye="password_confirmation" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
            </div>
            <p class="hint" id="match" aria-live="polite"></p>
        </div>

        <button type="submit" class="btn-main"><span class="spin"></span><span class="label">Create account</span></button>
    </form>

    <p class="switch">Already have an account? <a href="{{ route('login') }}" class="link">Log in</a></p>

@endsection

@push('scripts')
<script>
(function () {
    var form = document.querySelector('form'),
        pw = document.getElementById('password'),
        cf = document.getElementById('password_confirmation'),
        meter = document.getElementById('meter'),
        st = document.getElementById('strength'),
        mt = document.getElementById('match');

    var labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];

    // Password strength bar
    function strength() {
        var v = pw.value, s = 0;
        var len = v.length >= 8, letter = /[A-Za-z]/.test(v), num = /[0-9]/.test(v);

        if (v.length) s = 1;
        if (len && letter && num) s = 2;
        if (s === 2 && /[a-z]/.test(v) && /[A-Z]/.test(v)) s = 3;
        if (s === 3 && (/[^A-Za-z0-9]/.test(v) || v.length >= 12)) s = 4;

        meter.dataset.s = s;
        st.textContent = labels[s];
    }

    // Confirm password check
    function match() {
        if (!cf.value) { mt.textContent = ''; mt.className = 'hint'; return; }
        var ok = cf.value === pw.value;
        mt.textContent = ok ? 'Passwords match' : 'Passwords do not match';
        mt.className = 'hint ' + (ok ? 'ok' : 'bad');
    }

    pw.addEventListener('input', function () { strength(); match(); });
    cf.addEventListener('input', match);

    // Stop the submit if the two passwords differ (runs before the loading state)
    form.addEventListener('submit', function (e) {
        if (pw.value !== cf.value) {
            e.preventDefault();
            match();
            cf.focus();
        }
    });
})();
</script>
@endpush