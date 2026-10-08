@extends('layouts.auth')

@section('title', 'Log in')

@section('content')

    <h1>Welcome <span class="grad">back</span></h1>
    <p class="sub">Log in to your planner.</p>

    @if (session('status'))
        <div class="alert ok" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert err" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" data-busy="Logging in...">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" class="{{ $errors->has('email') ? 'bad' : '' }}" required autofocus>
            </div>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password" class="{{ $errors->has('password') ? 'bad' : '' }}" required>
                <button type="button" class="eye" data-eye="password" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
            </div>
            <p class="hint" id="caps"></p>
        </div>

        <div class="row">
            <label class="check"><input type="checkbox" name="remember" value="1" checked> Keep me logged in</label>
            <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
        </div>

        <button type="submit" class="btn-main"><span class="spin"></span><span class="label">Log in</span></button>
    </form>

    <p class="switch">New here? <a href="{{ route('register') }}" class="link">Create an account</a></p>

@endsection

@push('scripts')
<script>
(function () {
    // Caps Lock warning
    var pw = document.getElementById('password'), caps = document.getElementById('caps');

    function check(e) {
        var on = e.getModifierState && e.getModifierState('CapsLock');
        caps.textContent = on ? 'Caps Lock is on' : '';
        caps.className = 'hint' + (on ? ' warn' : '');
    }

    pw.addEventListener('keydown', check);
    pw.addEventListener('keyup', check);
    pw.addEventListener('blur', function () { caps.textContent = ''; caps.className = 'hint'; });
})();
</script>
@endpush