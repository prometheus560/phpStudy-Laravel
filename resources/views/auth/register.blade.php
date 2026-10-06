<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Study Planner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root {
    --bg: #07070d;
    --card: rgba(20,20,31,.78);
    --line: rgba(255,255,255,.08);
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
    font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;

    background:
        radial-gradient(
            60rem 40rem at 8% -10%,
            rgba(59,130,246,.20),
            transparent 60%
        ),
        radial-gradient(
            50rem 40rem at 110% 110%,
            rgba(99,102,241,.16),
            transparent 60%
        ),
        var(--bg);

    -webkit-font-smoothing: antialiased;
}

.shell {
    width: 100%;
    max-width: 1080px;
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 72px;
    align-items: center;
}

.logo {
    width: 64px;
    height: 64px;
    background: #fff;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow:
        0 10px 30px rgba(59,130,246,.25),
        0 0 0 1px rgba(255,255,255,.1);
    margin-bottom: 28px;
}

.logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.brand h1 {
    font-size: clamp(34px,4.4vw,52px);
    line-height: 1.08;
    font-weight: 700;
    letter-spacing: -.03em;
    color: #fff;
    margin-bottom: 16px;
}

.brand h1 span {
    background: linear-gradient(90deg,#60a5fa,#818cf8);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.lead {
    color: var(--muted);
    font-size: 18px;
    line-height: 1.6;
    max-width: 480px;
}

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
    background: rgba(59,130,246,.1);
    border: 1px solid rgba(59,130,246,.28);
    color: #7db4ff;
    flex: none;
}

.feats svg {
    width: 19px;
    height: 19px;
}

.preview {
    margin-top: 34px;
    max-width: 400px;
    padding: 16px 18px;
    border-radius: 16px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--line);
}

.preview small {
    display: block;
    color: var(--muted);
    font-size: 11px;
    letter-spacing: .08em;
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
    background: rgba(251,191,36,.12);
    color: var(--amber);
}

.chip.cool {
    background: rgba(74,222,128,.12);
    color: var(--green);
}

.card {
    background: var(--card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 40px;
    box-shadow:
        0 30px 80px rgba(0,0,0,.55),
        inset 0 1px 0 rgba(255,255,255,.05);
}

.card h2 {
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -.02em;
    color: #fff;
    margin-bottom: 6px;
}

.sub {
    color: var(--muted);
    font-size: 14px;
    margin-bottom: 26px;
}

.alert {
    padding: 12px 14px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 13px;
    line-height: 1.5;
}

.alert.err {
    background: rgba(248,113,113,.1);
    color: #fca5a5;
    border: 1px solid rgba(248,113,113,.25);
}

.alert.ok {
    background: rgba(74,222,128,.1);
    color: #86efac;
    border: 1px solid rgba(74,222,128,.25);
}

.alert ul {
    padding-left: 18px;
}

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
    transition: color .2s;
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
        border-color .2s,
        box-shadow .2s,
        background .2s;
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
    box-shadow: 0 0 0 4px rgba(59,130,246,.16);
}

.control:focus-within .ic {
    color: #7db4ff;
}

.control input.bad {
    border-color: var(--red);
}

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
    color: #fff;
    background: rgba(255,255,255,.06);
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

.meter {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 6px;
    margin-top: 10px;
}

.meter span {
    height: 4px;
    border-radius: 99px;
    background: #232334;
    transition: background .25s;
}

.meter[data-s="1"] span:nth-child(-n+1) {
    background: var(--red);
}

.meter[data-s="2"] span:nth-child(-n+2) {
    background: #fb923c;
}

.meter[data-s="3"] span:nth-child(-n+3) {
    background: var(--amber);
}

.meter[data-s="4"] span {
    background: var(--green);
}

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
    transition: .2s;
}

.rules span.ok {
    color: var(--green);
    border-color: rgba(74,222,128,.4);
    background: rgba(74,222,128,.08);
}

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
        transform .15s,
        box-shadow .2s,
        background .2s;
}

.btn.primary {
    color: #fff;
    background: linear-gradient(135deg,#3b82f6,#4f46e5);
    box-shadow: 0 10px 28px rgba(59,130,246,.35);
}

.btn.primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 34px rgba(59,130,246,.45);
}

.btn.primary:active {
    transform: translateY(0);
}

.btn:disabled {
    opacity: .75;
    cursor: wait;
    transform: none;
}

.btn.ghost {
    height: 46px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--field-line);
    color: #cfd2e0;
    font-weight: 600;
    font-size: 14px;
}

.btn.ghost:hover {
    border-color: var(--blue);
    color: #a9cdff;
    background: rgba(59,130,246,.08);
}

.spin {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: r .7s linear infinite;
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

.or {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 22px 0;
    color: #5f6479;
    font-size: 12px;
    letter-spacing: .08em;
}

.or::before,
.or::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--line);
}

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
    * {
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
        <h1>Start your<br><span>study journey.</span></h1>
        <p class="lead">Create your free account and start organizing your subjects, tasks and study schedule in minutes.</p>
        <ul class="feats"><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></i>Organize all your subjects in one place</li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></i>Never miss a deadline with live countdowns</li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></i>Focus on what matters with priorities</li></ul><div class="preview" aria-hidden="true"><small>Sample preview</small><div class="prow"><span>Research paper</span><span class="chip hot">Due tomorrow</span></div><div class="prow"><span>Design project</span><span class="chip cool">12 days left</span></div></div>
    </section>

    <section class="card">
        <h2>Create new account</h2>
        <p class="sub">It's quick and easy to get started.</p>

            @if ($errors->any())
                <div class="alert err" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        <form method="POST" action="{{ url('/register') }}" id="registerForm">
            @csrf

            <div class="field">
                <label for="name">Full name</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" maxlength="100" autocomplete="name" class="{{ $errors->has('name') ? 'bad' : '' }}" required autofocus>
                </div>
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" maxlength="100" autocomplete="email" class="{{ $errors->has('email') ? 'bad' : '' }}" required>
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="Create a password" autocomplete="new-password" class="{{ $errors->has('password') ? 'bad' : '' }}" required>
                    <button type="button" class="eye" data-eye="password" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
                </div>
                <div class="meter" id="meter" data-s="0" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
                <p class="hint" id="strength" aria-live="polite">Use at least 8 characters.</p>
                <div class="rules">
                    <span id="rLen">8+ characters</span>
                    <span id="rLet">A letter</span>
                    <span id="rNum">A number</span>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <div class="control">
                    <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" autocomplete="new-password" required>
                    <button type="button" class="eye" data-eye="password_confirmation" aria-label="Show password"><svg class="open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg><svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.9 10.9 0 0 1 12 19c-7 0-11-7-11-7a19 19 0 0 1 5.06-5.94M9.9 4.24A10.9 10.9 0 0 1 12 5c7 0 11 7 11 7a19 19 0 0 1-3.16 4.19M1 1l22 22"/></svg></button>
                </div>
                <p class="hint" id="match" aria-live="polite"></p>
            </div>

            <button type="submit" class="btn primary"><span class="spin"></span><span class="label">Create account</span></button>
        </form>

        <div class="or">OR</div>
        <a href="{{ route('login') }}" class="btn ghost">Already have an account? Log in</a>

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

    var pw = document.getElementById('password'), cf = document.getElementById('password_confirmation');
    var meter = document.getElementById('meter'), st = document.getElementById('strength'), mt = document.getElementById('match');
    var labels = ['Use at least 8 characters.', 'Weak password', 'Fair password', 'Good password', 'Strong password'];

    function strength() {
        var v = pw.value, len = v.length >= 8, let_ = /[A-Za-z]/.test(v), num = /[0-9]/.test(v);
        document.getElementById('rLen').classList.toggle('ok', len);
        document.getElementById('rLet').classList.toggle('ok', let_);
        document.getElementById('rNum').classList.toggle('ok', num);
        var s = 0;
        if (v.length) s = 1;
        if (len && let_ && num) s = 2;
        if (s === 2 && (/[a-z]/.test(v) && /[A-Z]/.test(v))) s = 3;
        if (s === 3 && (/[^A-Za-z0-9]/.test(v) || v.length >= 12)) s = 4;
        meter.dataset.s = s;
        st.textContent = labels[s];
    }
    function match() {
        if (!cf.value) { mt.textContent = ''; mt.className = 'hint'; return; }
        var ok = cf.value === pw.value;
        mt.textContent = ok ? 'Passwords match' : 'Passwords do not match';
        mt.className = 'hint ' + (ok ? 'ok' : 'bad');
    }
    pw.addEventListener('input', function () { strength(); match(); });
    cf.addEventListener('input', match);
    form.addEventListener('submit', function (e) {
        if (pw.value !== cf.value) { e.preventDefault(); match(); cf.focus(); return; }
        busy('Creating account...');
    });
})();
</script>
</body>
</html>