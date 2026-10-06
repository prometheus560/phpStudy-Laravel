<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Study Planner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root {
    --bg: #07070d;
    --card: rgba(20, 20, 31, 0.78);
    --line: rgba(255, 255, 255, 0.08);
    --field: #12121c;
    --field-line: #262636;
    --text: #e7e9f0;
    --muted: #8b90a5;
    --blue: #3b82f6;
    --indigo: #6366f1;
    --red: #f87171;
    --green: #4ade80;
    --amber: #fbbf24;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html {
    color-scheme: dark;
}

body {
    font-family: "Inter", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
    background:
        radial-gradient(
            60rem 40rem at 8% -10%,
            rgba(59, 130, 246, 0.2),
            transparent 60%
        ),
        radial-gradient(
            50rem 40rem at 110% 110%,
            rgba(99, 102, 241, 0.16),
            transparent 60%
        ),
        var(--bg);
    -webkit-font-smoothing: antialiased;
}

/* Main layout */
.shell {
    width: 100%;
    max-width: 1080px;
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 72px;
    align-items: center;
}

/* FocusNest logo */
.logo {
    width: 64px;
    height: 64px;
    background: #ffffff;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow:
        0 10px 30px rgba(59, 130, 246, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    margin-bottom: 28px;
}

.logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Brand */
.brand h1 {
    font-size: clamp(34px, 4.4vw, 52px);
    line-height: 1.08;
    font-weight: 700;
    letter-spacing: -0.03em;
    color: #ffffff;
    margin-bottom: 16px;
}

.brand h1 span {
    color: #818cf8;
}

.lead {
    color: var(--muted);
    font-size: 18px;
    line-height: 1.6;
    max-width: 480px;
}

/* Features */
.feats {
    list-style: none;
    margin-top: 32px;
    display: grid;
    gap: 14px;
}

.feats li {
    display: flex;
    align-items: center;
    gap: 14px;
    color: #c9ccda;
    font-size: 15px;
}

.feats i {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.28);
    color: #7db4ff;
    flex: none;
}

.feats svg {
    width: 19px;
    height: 19px;
}

/* Preview */
.preview {
    margin-top: 34px;
    max-width: 400px;
    padding: 16px 18px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--line);
}

.preview small {
    display: block;
    color: var(--muted);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.prow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 9px 0;
    font-size: 14px;
    border-top: 1px solid var(--line);
}

.prow:first-of-type {
    border-top: 0;
}

.chip {
    font-size: 11px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 99px;
    white-space: nowrap;
}

.chip.hot {
    background: rgba(251, 191, 36, 0.12);
    color: var(--amber);
}

.chip.cool {
    background: rgba(74, 222, 128, 0.12);
    color: var(--green);
}

/* Form card */
.card {
    background: var(--card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 40px;
    box-shadow:
        0 30px 80px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.card h2 {
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin-bottom: 6px;
}

.sub {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 26px;
}

/* Alerts */
.alert {
    padding: 12px 14px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 13px;
    line-height: 1.5;
}

.alert.err {
    background: rgba(248, 113, 113, 0.1);
    color: #fca5a5;
    border: 1px solid rgba(248, 113, 113, 0.25);
}

.alert.ok {
    background: rgba(74, 222, 128, 0.1);
    color: #86efac;
    border: 1px solid rgba(74, 222, 128, 0.25);
}

.alert ul {
    padding-left: 18px;
}

/* Form fields */
.field {
    margin-bottom: 18px;
}

.field > label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #cfd2e0;
    margin-bottom: 8px;
}

.control {
    position: relative;
}

.control .ic {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: #6d7288;
    pointer-events: none;
    transition: color 0.2s;
}

.control input {
    width: 100%;
    height: 48px;
    padding: 0 46px 0 44px;
    background: var(--field);
    border: 1px solid var(--field-line);
    border-radius: 12px;
    color: #f4f5fa;
    font: inherit;
    font-size: 15px;
    transition:
        border-color 0.2s,
        box-shadow 0.2s,
        background 0.2s;
}

.control input::placeholder {
    color: #5f6479;
}

.control input:hover {
    border-color: #34364a;
}

.control input:focus {
    outline: 0;
    border-color: var(--blue);
    background: #14141f;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.16);
}

.control:focus-within .ic {
    color: #7db4ff;
}

.control input.bad {
    border-color: var(--red);
}

/* Password eye */
.eye {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border: 0;
    background: transparent;
    color: #7a7f95;
    border-radius: 9px;
    cursor: pointer;
    display: grid;
    place-items: center;
}

.eye:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
}

.eye svg {
    width: 18px;
    height: 18px;
}

.eye .off {
    display: none;
}

.eye.on .off {
    display: block;
}

.eye.on .open {
    display: none;
}

/* Hints */
.hint {
    font-size: 12px;
    margin-top: 7px;
    min-height: 16px;
    color: var(--muted);
}

.hint.warn {
    color: var(--amber);
}

.hint.ok {
    color: var(--green);
}

.hint.bad {
    color: var(--red);
}

/* Password strength */
.meter {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-top: 10px;
}

.meter span {
    height: 4px;
    border-radius: 99px;
    background: #232334;
    transition: background 0.25s;
}

.meter[data-s="1"] span:nth-child(-n + 1) {
    background: var(--red);
}

.meter[data-s="2"] span:nth-child(-n + 2) {
    background: #fb923c;
}

.meter[data-s="3"] span:nth-child(-n + 3) {
    background: var(--amber);
}

.meter[data-s="4"] span {
    background: var(--green);
}

/* Password rules */
.rules {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.rules span {
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 99px;
    border: 1px solid var(--field-line);
    color: var(--muted);
    transition: 0.2s;
}

.rules span.ok {
    color: var(--green);
    border-color: rgba(74, 222, 128, 0.4);
    background: rgba(74, 222, 128, 0.08);
}

/* Remember / forgot */
.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 2px 0 22px;
    font-size: 13px;
}

.check {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--muted);
    cursor: pointer;
}

.check input {
    width: 16px;
    height: 16px;
    accent-color: var(--blue);
}

.link {
    color: #7db4ff;
    text-decoration: none;
    font-weight: 600;
}

.link:hover {
    color: #a9cdff;
    text-decoration: underline;
}

/* Buttons */
.btn {
    width: 100%;
    height: 50px;
    border: 0;
    border-radius: 12px;
    font: inherit;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    transition:
        transform 0.15s,
        box-shadow 0.2s,
        background 0.2s;
}

.btn.primary {
    color: #ffffff;
    background: linear-gradient(135deg, #3b82f6, #4f46e5);
    box-shadow: 0 10px 28px rgba(59, 130, 246, 0.35);
}

.btn.primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 34px rgba(59, 130, 246, 0.45);
}

.btn.primary:active {
    transform: translateY(0);
}

.btn:disabled {
    opacity: 0.75;
    cursor: wait;
    transform: none;
}

.btn.ghost {
    height: 46px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--field-line);
    color: #cfd2e0;
    font-weight: 600;
    font-size: 14px;
}

.btn.ghost:hover {
    border-color: var(--blue);
    color: #a9cdff;
    background: rgba(59, 130, 246, 0.08);
}

/* Loading spinner */
.spin {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: r 0.7s linear infinite;
    display: none;
}

.btn.loading .spin {
    display: block;
}

@keyframes r {
    to {
        transform: rotate(360deg);
    }
}

/* Divider */
.or {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 22px 0;
    color: #5f6479;
    font-size: 12px;
    letter-spacing: 0.08em;
}

.or::before,
.or::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--line);
}

/* Footer */
.foot {
    text-align: center;
    margin-top: 22px;
    font-size: 12px;
    color: #5f6479;
}

:focus-visible {
    outline: 2px solid #7db4ff;
    outline-offset: 2px;
}

/* Tablet / mobile */
@media (max-width: 900px) {
    body {
        padding: 20px;
        align-items: flex-start;
    }

    .shell {
        grid-template-columns: 1fr;
        gap: 28px;
        max-width: 480px;
        margin: auto;
    }

    .brand {
        text-align: center;
    }

    .logo {
        margin: 0 auto 18px;
        width: 56px;
        height: 56px;
    }

    .lead {
        margin: auto;
        font-size: 16px;
    }

    .feats,
    .preview {
        display: none;
    }

    .brand h1 {
        font-size: 34px;
    }
}

@media (max-width: 450px) {
    .card {
        padding: 28px 20px;
        border-radius: 18px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation: none !important;
        transition: none !important;
    }
}
</style>
</head>
<body>
<main class="shell">

    <section class="brand">
        <div class="logo"><img src="{{ asset('logo.jpg') }}" alt="Study Planner logo"></div>
        <h1>Plan smarter.<br><span>Study better.</span></h1>
        <p class="lead">Your subjects, tasks and deadlines in one clean workspace, so you always know what to do next.</p>
        <ul class="feats"><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></i>Organize all your subjects in one place</li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></i>Never miss a deadline with live countdowns</li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></i>Focus on what matters with priorities</li></ul><div class="preview" aria-hidden="true"><small>Sample preview</small><div class="prow"><span>Research paper</span><span class="chip hot">Due tomorrow</span></div><div class="prow"><span>Design project</span><span class="chip cool">12 days left</span></div></div>
    </section>

    <section class="card">
        <h2>Welcome back</h2>
        <p class="sub">Log in to continue to your Study Planner.</p>

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

        <form method="POST" action="{{ url('/login') }}" id="loginForm">
            @csrf

            <div class="field">
                <label for="email">Email address</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" class="{{ $errors->has('email') ? 'bad' : '' }}" required autofocus>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" class="{{ $errors->has('password') ? 'bad' : '' }}" required>
                    <button type="button" class="eye" data-eye="password" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
                </div>
                <p class="hint" id="caps"></p>
            </div>

            <div class="row">
                <label class="check"><input type="checkbox" name="remember" value="1" checked> Keep me logged in</label>
                <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
            </div>

            <button type="submit" class="btn primary"><span class="spin"></span><span class="label">Log in</span></button>
        </form>

        <div class="or">OR</div>
        <a href="{{ route('register') }}" class="btn ghost">Create new account</a>

        <p class="foot">Study Planner &middot; Stay organized &middot; Study smarter</p>
    </section>

</main>

<script>
(function () {
    document.querySelectorAll('[data-eye]').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.eye), show = i.type === 'password';
            i.type = show ? 'text' : 'password';
            b.classList.toggle('on', show);
            b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
    var form = document.querySelector('form'), btn = form.querySelector('.btn.primary'), lbl = btn.querySelector('.label'), idle = lbl.textContent;
    function busy(t) { btn.disabled = true; btn.classList.add('loading'); lbl.textContent = t; }
    window.addEventListener('pageshow', function () { btn.disabled = false; btn.classList.remove('loading'); lbl.textContent = idle; });

    var pw = document.getElementById('password'), caps = document.getElementById('caps');
    function c(e) {
        var on = e.getModifierState && e.getModifierState('CapsLock');
        caps.textContent = on ? 'Caps Lock is on' : '';
        caps.className = 'hint' + (on ? ' warn' : '');
    }
    pw.addEventListener('keydown', c); pw.addEventListener('keyup', c);
    pw.addEventListener('blur', function () { caps.textContent = ''; caps.className = 'hint'; });
    form.addEventListener('submit', function () { busy('Logging in...'); });
})();
</script>
</body>
</html>