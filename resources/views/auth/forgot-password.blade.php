@extends('layouts.auth')

@section('title', 'Forgot password')

@section('content')

    <h1>Forgot <span class="grad">password?</span></h1>
    <p class="sub">Enter your email and we'll send you a reset link.</p>

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

    <form method="POST" action="{{ route('password.email') }}" data-busy="Sending...">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <div class="control">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" maxlength="100" autocomplete="email" class="{{ $errors->has('email') ? 'bad' : '' }}" required autofocus>
            </div>
        </div>

        <button type="submit" class="btn-main" style="margin-top:6px"><span class="spin"></span><span class="label">Send reset link</span></button>
    </form>

    <p class="switch"><a href="{{ route('login') }}" class="link">&larr; Back to login</a></p>

@endsection