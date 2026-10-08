<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Study Planner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #05050a;
            --line: rgba(255,255,255,.08);
            --field: #0f0f18;
            --field-line: #262636;
            --text: #e7e9f0;
            --muted: #8b90a5;
            --blue: #3b82f6;
            --red: #f87171;
            --green: #4ade80;
            --amber: #fbbf24;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { color-scheme: dark; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Animated background */
        .bg { position: fixed; inset: 0; overflow: hidden; z-index: 0; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: .42; animation: drift 18s ease-in-out infinite alternate; }
        .orb.a { width: 520px; height: 520px; background: #2563eb; top: -170px; left: -130px; }
        .orb.b { width: 480px; height: 480px; background: #6d28d9; bottom: -190px; right: -130px; animation-delay: -6s; }
        .orb.c { width: 300px; height: 300px; background: #0ea5e9; top: 42%; left: 58%; opacity: .2; animation-delay: -12s; }
        @keyframes drift { to { transform: translate(60px, 40px) scale(1.12); } }
        .grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 46px 46px;
            -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 70%);
            mask-image: radial-gradient(ellipse at center, #000 15%, transparent 70%);
        }

        /*  Layout */
        .wrap { position: relative; z-index: 1; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 28px 18px; }

        .auth-card {
            position: relative;
            width: 100%;
            max-width: 420px;
            padding: 38px 34px 30px;
            border-radius: 26px;
            background: darkblue;
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            box-shadow: 0 40px 100px black, inset 0 1px 0 rgba(255,255,255,.06);
            animation: rise .6s cubic-bezier(.2,.8,.2,1) both;
        }
        /* gradient border */
        .auth-card::before {
            content: ""; position: absolute; inset: 0; border-radius: inherit; padding: 1px;
            background: linear-gradient(160deg, rgba(125,180,255,.55), rgba(255,255,255,.06) 38%, rgba(129,140,248,.4));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
            pointer-events: none;
        }
        @keyframes rise { from { opacity: 0; transform: translateY(16px); } }

        .logo {
            width: 68px; height: 68px; margin: 0 auto 22px;
            background: #fff; border-radius: 19px; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 1px rgba(255,255,255,.14), 0 14px 44px rgba(59,130,246,.45);
            animation: float 6s ease-in-out infinite;
        }
        .logo img { width: 100%; height: 100%; object-fit: contain; }
        @keyframes float { 50% { transform: translateY(-5px); } }

        h1 { text-align: center; font-size: 28px; font-weight: 700; letter-spacing: -.03em; color: #fff; line-height: 1.15; }
        h1 .grad { background: linear-gradient(90deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .sub { text-align: center; color: var(--muted); font-size: 14px; margin: 8px 0 26px; }

        /* Alerts  */
        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px; line-height: 1.5; }
        .alert.err { background: rgba(248,113,113,.1); color: #fca5a5; border: 1px solid rgba(248,113,113,.25); }
        .alert.ok { background: rgba(74,222,128,.1); color: #86efac; border: 1px solid rgba(74,222,128,.25); }
        .alert ul { padding-left: 18px; }

        /*  Fields  */
        .field { margin-bottom: 16px; }
        .field > label { display: block; font-size: 12.5px; font-weight: 600; color: #b9bdd0; margin-bottom: 7px; }
        .control { position: relative; }
        .control .ic { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #6d7288; pointer-events: none; transition: color .2s; }
        .control input {
            width: 100%; height: 48px; padding: 0 46px 0 44px;
            background: var(--field); border: 1px solid var(--field-line); border-radius: 13px;
            color: #f4f5fa; font: inherit; font-size: 15px;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .control input::placeholder { color: #565b70; }
        .control input:hover { border-color: #34364a; }
        .control input:focus { outline: 0; border-color: var(--blue); background: #11111c; box-shadow: 0 0 0 4px rgba(59,130,246,.16); }
        .control:focus-within .ic { color: #7db4ff; }
        .control input.bad { border-color: var(--red); }

        .eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; background: transparent; color: #7a7f95; border-radius: 9px; cursor: pointer; display: grid; place-items: center; }
        .eye:hover { color: #fff; background: rgba(255,255,255,.06); }
        .eye svg { width: 18px; height: 18px; }
        .eye .off { display: none; }
        .eye.on .off { display: block; }
        .eye.on .open { display: none; }

        .hint { font-size: 12px; margin-top: 7px; min-height: 16px; color: var(--muted); }
        .hint.warn { color: var(--amber); }
        .hint.ok { color: var(--green); }
        .hint.bad { color: var(--red); }

        .meter { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-top: 10px; }
        .meter span { height: 4px; border-radius: 99px; background: #232334; transition: background .25s; }
        .meter[data-s="1"] span:nth-child(-n+1) { background: var(--red); }
        .meter[data-s="2"] span:nth-child(-n+2) { background: var(--orange); }
        .meter[data-s="3"] span:nth-child(-n+3) { background: var(--amber); }
        .meter[data-s="4"] span { background: var(--green); }

        .row { display: flex; justify-content: space-between; align-items: center; margin: 4px 0 22px; font-size: 13px; }
        .check { display: flex; align-items: center; gap: 8px; color: var(--muted); cursor: pointer; }
        .check input { width: 16px; height: 16px; accent-color: var(--blue); }
        .link { color: #7db4ff; text-decoration: none; font-weight: 600; }
        .link:hover { color: #a9cdff; text-decoration: underline; }

        /* Button */
        .btn-main {
            position: relative; overflow: hidden;
            width: 100%; height: 50px; border: 0; border-radius: 13px;
            font: inherit; font-size: 15px; font-weight: 600; color: #fff;
            background: linear-gradient(135deg, #3b82f6, #6366f1 60%, #7c3aed);
            box-shadow: 0 12px 30px rgba(79,70,229,.4);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            cursor: pointer; transition: transform .15s, box-shadow .2s;
        }
        .btn-main::after {
            content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.28), transparent);
            transform: skewX(-20deg); transition: left .6s;
        }
        .btn-main:hover { transform: translateY(-1px); box-shadow: 0 16px 38px rgba(79,70,229,.55); }
        .btn-main:hover::after { left: 120%; }
        .btn-main:active { transform: translateY(0); }
        .btn-main:disabled { opacity: .8; cursor: wait; transform: none; }

        .spin { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.35); border-top-color: #fff; border-radius: 50%; animation: r .7s linear infinite; display: none; }
        .btn-main.loading .spin { display: block; }
        @keyframes r { to { transform: rotate(360deg); } }

        .switch { text-align: center; margin-top: 22px; font-size: 13.5px; color: var(--muted); }

        :focus-visible { outline: 2px solid #7db4ff; outline-offset: 2px; }
        @media (max-width: 450px) { .auth-card { padding: 30px 22px 24px; border-radius: 22px; } h1 { font-size: 25px; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
    </style>
</head>
<body>

    <div class="bg" aria-hidden="true">
        <i class="orb a"></i><i class="orb b"></i><i class="orb c"></i><i class="grid"></i>
    </div>

    <main class="wrap">
        <section class="auth-card">
            <div class="logo"><img src="{{ asset('logo.jpg') }}" alt="Logo"></div>
            @yield('content')
        </section>
    </main>

    {{-- Page scripts first, so they can stop a submit before the loading state starts --}}
    @stack('scripts')

    <script>
    (function () {
        // Show / hide password buttons
        document.querySelectorAll('[data-eye]').forEach(function (b) {
            b.addEventListener('click', function () {
                var i = document.getElementById(b.dataset.eye), show = i.type === 'password';
                i.type = show ? 'text' : 'password';
                b.classList.toggle('on', show);
                b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        // Loading state on the main button
        var form = document.querySelector('form[data-busy]');
        if (!form) return;

        var btn = form.querySelector('.btn-main'),
            lbl = btn.querySelector('.label'),
            idle = lbl.textContent;

        window.addEventListener('pageshow', function () {
            btn.disabled = false;
            btn.classList.remove('loading');
            lbl.textContent = idle;
        });

        form.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            btn.disabled = true;
            btn.classList.add('loading');
            lbl.textContent = form.dataset.busy;
        });
    })();
    </script>

</body>
</html>