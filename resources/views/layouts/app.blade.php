<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Classtrack</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --card: rgba(14,13,30,.70);
            --line: rgba(190,175,255,.10);
            --field: #100f1d;
            --field-line: #2a2842;
            --muted: #8b90a5;
            --violet: #a78bfa;
            --blue: #60a5fa;
        }

        html { color-scheme: dark; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            background:
                radial-gradient(48rem 34rem at 6% -10%, rgba(79,70,229,.22), transparent 60%),
                radial-gradient(40rem 30rem at 106% 10%, rgba(147,51,234,.16), transparent 60%),
                radial-gradient(52rem 38rem at 100% 110%, rgba(67,56,202,.22), transparent 60%),
                #04050c;
            background-attachment: fixed;
        }

        h1, h2, h3, .display { font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif; }

        /* ---------- Sidebar ---------- */
        .sidebar-glass { background: rgba(8,8,20,.80); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-color: var(--line); }

        .sidebar-scroll { scrollbar-width: thin; scrollbar-color: rgba(167,139,250,.35) transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(167,139,250,.30); border-radius: 3px; }

        .brand { display: flex; align-items: center; gap: 14px; padding: 22px 22px 20px; border-bottom: 1px solid var(--line); text-decoration: none; }
        .brand-logo { width: 46px; height: 46px; border-radius: 50%; padding: 2px; flex-shrink: 0; background: linear-gradient(135deg, #6366f1, #c084fc); box-shadow: 0 0 24px rgba(139,92,246,.35); }
        .brand-logo span { display: block; width: 100%; height: 100%; border-radius: 50%; overflow: hidden; background: #fff; }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand-name { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 17px; color: #fff; line-height: 1.15; }
        .brand-sub { display: block; font-size: 13px; color: var(--muted); margin-top: 2px; }

        .nav-label { display: flex; align-items: center; gap: 10px; padding: 18px 12px 8px; font-size: 12px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: #8b90a5; }
        .nav-label::before { content: "\2726"; font-size: 10px; color: #7c6cf0; }
        .nav-label::after { content: ""; flex: 1; height: 1px; background: linear-gradient(90deg, rgba(129,140,248,.35), transparent); }

        .nav-link { display: flex; align-items: center; gap: 14px; padding: 11px 14px; margin-bottom: 2px; border-radius: 12px; border: 1px solid transparent; font-size: 15px; color: #aab0c4; text-decoration: none; transition: background .2s, color .2s, border-color .2s; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,.05); }
        .nav-link.is-active { color: #ddd6fe; font-weight: 500; background: rgba(139,92,246,.12); border-color: rgba(167,139,250,.28); }
        .nav-link svg { width: 20px; height: 20px; flex-shrink: 0; }

        .user-card { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-top: 1px solid var(--line); }
        .avatar { width: 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; font-weight: 700; font-size: 14px; color: #c7d2fe; background: rgba(79,70,229,.22); border: 1px solid rgba(129,140,248,.40); box-shadow: 0 0 0 3px rgba(79,70,229,.10); }

        /* ---------- Shared components (used by other pages) ---------- */
        .glass { background: var(--card); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--line); border-radius: 22px; box-shadow: 0 30px 80px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05); }
        .glass-accent { border-color: rgba(129,140,248,.34); box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 60px rgba(59,130,246,.08), inset 0 1px 0 rgba(255,255,255,.06); }

        .page-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 30px; font-weight: 800; letter-spacing: -.02em; color: #fff; }
        .page-sub { color: var(--muted); font-size: 14px; margin-top: 4px; }
        .grad-text { background: linear-gradient(90deg, #60a5fa, #a78bfa 70%, #d8a8ff); -webkit-background-clip: text; background-clip: text; color: transparent; }

        .f-label { display: block; font-size: 13px; font-weight: 600; color: #cfd2e0; margin-bottom: 8px; }
        .control { position: relative; }
        .control .ic { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #6d7288; pointer-events: none; transition: color .2s; }
        .control:focus-within .ic { color: #7db4ff; }
        .f-input { width: 100%; height: 48px; padding: 0 14px; background: var(--field); border: 1px solid var(--field-line); border-radius: 12px; color: #f4f5fa; font: inherit; font-size: 15px; transition: border-color .2s, box-shadow .2s, background .2s; }
        .f-input.has-icon { padding-left: 44px; }
        .f-input::placeholder { color: #5f6479; }
        .f-input:hover { border-color: #34364a; }
        .f-input:focus { outline: 0; border-color: #3b82f6; background: #14141f; box-shadow: 0 0 0 4px rgba(59,130,246,.16); }
        .f-input[type=date] { position: relative; }
        .f-input[type=date]::-webkit-calendar-picker-indicator { position: absolute; inset: 0; width: auto; height: auto; opacity: 0; cursor: pointer; }

        .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 48px; padding: 0 20px; border: 0; border-radius: 14px; font: inherit; font-size: 15px; font-weight: 600; color: #fff; background: linear-gradient(135deg, #3b82f6, #4f46e5); box-shadow: 0 10px 28px rgba(59,130,246,.35); cursor: pointer; text-decoration: none; transition: transform .15s, box-shadow .2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 14px 34px rgba(59,130,246,.45); }
        .btn-primary:active { transform: translateY(0); }
        .btn-ghost { display: inline-flex; align-items: center; justify-content: center; height: 46px; padding: 0 18px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid var(--field-line); color: #cfd2e0; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: .2s; }
        .btn-ghost:hover { border-color: #3b82f6; color: #a9cdff; background: rgba(59,130,246,.08); }

        .btn-sm { display: inline-flex; align-items: center; height: 36px; padding: 0 14px; border-radius: 10px; border: 1px solid transparent; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: .2s; }
        .btn-ok { background: rgba(74,222,128,.10); color: #4ade80; border-color: rgba(74,222,128,.30); }
        .btn-ok:hover { background: rgba(74,222,128,.18); }
        .btn-warn { background: rgba(251,146,60,.10); color: #fb923c; border-color: rgba(251,146,60,.30); }
        .btn-warn:hover { background: rgba(251,146,60,.18); }
        .btn-danger { background: rgba(248,113,113,.08); color: #fca5a5; border-color: rgba(248,113,113,.30); }
        .btn-danger:hover { background: rgba(248,113,113,.16); }

        .chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 99px; white-space: nowrap; }
        .chip-high { background: rgba(248,113,113,.12); color: #fca5a5; }
        .chip-med { background: rgba(251,191,36,.12); color: #fbbf24; }
        .chip-low { background: rgba(74,222,128,.12); color: #4ade80; }
        .chip-warn { background: rgba(251,146,60,.12); color: #fb923c; }
        .chip-blue { background: rgba(59,130,246,.12); color: #7db4ff; }
        .chip-muted { background: rgba(255,255,255,.06); color: #9ca3b8; }

        .alert { padding: 12px 14px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; line-height: 1.5; }
        .alert-err { background: rgba(248,113,113,.10); color: #fca5a5; border: 1px solid rgba(248,113,113,.25); }
        .alert-ok { background: rgba(74,222,128,.10); color: #86efac; border: 1px solid rgba(74,222,128,.25); }

        .stat { position: relative; overflow: hidden; padding: 20px; border-radius: 18px; background: var(--card); border: 1px solid var(--line); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); box-shadow: inset 0 1px 0 rgba(255,255,255,.05); transition: transform .2s, border-color .2s; }
        .stat::before { content: ""; position: absolute; right: -40px; top: -40px; width: 170px; height: 170px; border-radius: 50%; background: radial-gradient(circle, var(--glow), transparent 70%); pointer-events: none; }
        .stat:hover { transform: translateY(-2px); border-color: rgba(255,255,255,.16); }
        .stat-icon { position: relative; width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; color: var(--c); background: var(--bg); border: 1px solid var(--bd); }
        .stat-icon svg { width: 20px; height: 20px; }

        .row-item { border-top: 1px solid var(--line); transition: background .2s; }
        .row-item:hover { background: rgba(255,255,255,.025); }

        .bar { height: 6px; border-radius: 99px; background: rgba(255,255,255,.06); overflow: hidden; }
        .bar > i { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, #3b82f6, #6366f1); box-shadow: 0 0 12px rgba(99,102,241,.5); }

        .pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: #60a5fa; box-shadow: 0 0 0 0 rgba(96,165,250,.6); animation: pulse 2s infinite; }
        @keyframes pulse { 70% { box-shadow: 0 0 0 8px rgba(96,165,250,0); } 100% { box-shadow: 0 0 0 0 rgba(96,165,250,0); } }

        /* ---------- Dashboard look (hero, notice, stat strip, panels) ---------- */
        .hero { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 32px; padding: 48px 52px; border-radius: 30px; background: var(--card); border: 1px solid var(--line); box-shadow: inset 0 1px 0 rgba(255,255,255,.05); }
        .hero::before, .hero::after { content: ""; position: absolute; top: -30%; bottom: -30%; border: 1px solid rgba(190,175,255,.07); border-radius: 50%; pointer-events: none; }
        .hero::before { right: 22%; width: 60%; }
        .hero::after { right: 6%; width: 42%; }
        .hero > * { position: relative; z-index: 1; }
        .hero h1 { font-size: clamp(34px, 5.2vw, 64px); font-weight: 800; letter-spacing: -.035em; line-height: 1.02; color: #fff; }
        .hero h1 .name { display: block; background: linear-gradient(90deg, #4f8cff, #8b7cf6 55%, #d29bff); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .hero p { margin-top: 22px; font-size: 18px; color: #9aa0b8; }

        .ring { position: relative; width: 240px; height: 240px; flex-shrink: 0; }
        .ring svg { width: 100%; height: 100%; transform: rotate(-90deg); overflow: visible; }
        .ring .track { stroke: rgba(255,255,255,.06); }
        .ring .value { stroke: url(#ringGrad); stroke-linecap: round; filter: drop-shadow(0 0 10px rgba(139,92,246,.5)); }
        .ring-text { position: absolute; inset: 0; display: grid; place-content: center; text-align: center; }
        .ring-text b { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 56px; font-weight: 800; letter-spacing: -.03em; color: #fff; line-height: 1; }
        .ring-text span { margin-top: 6px; font-size: 14px; color: var(--muted); }

        .notice { display: flex; align-items: center; gap: 14px; padding: 16px 22px; border-radius: 16px; font-size: 16px; color: #f5f5fa; background: linear-gradient(90deg, rgba(251,191,36,.10), rgba(251,146,60,.03)); border: 1px solid rgba(251,191,36,.30); text-decoration: none; }
        .notice .dot { width: 12px; height: 12px; border-radius: 50%; background: #fbbf24; box-shadow: 0 0 12px rgba(251,191,36,.6); flex-shrink: 0; }
        .notice b { font-weight: 700; }
        .notice .go { margin-left: auto; color: #cfd2e0; }

        .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); border-radius: 24px; overflow: hidden; background: var(--card); border: 1px solid var(--line); }
        .stat-cell { position: relative; padding: 26px 28px 24px; border-left: 1px solid var(--line); }
        .stat-cell:first-child { border-left: 0; }
        .stat-cell::before { content: ""; position: absolute; left: 0; top: 22px; bottom: 22px; width: 3px; border-radius: 3px; background: var(--c); }
        .stat-cell b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 56px; font-weight: 800; line-height: 1; letter-spacing: -.03em; color: #fff; }
        .stat-cell span { display: flex; align-items: center; gap: 10px; margin-top: 14px; font-size: 16px; color: #c3c7d8; }
        .stat-cell svg { width: 20px; height: 20px; color: var(--c); }
        @media (max-width: 900px) {
            .stat-strip { grid-template-columns: repeat(2, 1fr); }
            .stat-cell:nth-child(3) { border-left: 0; }
            .stat-cell:nth-child(n+3) { border-top: 1px solid var(--line); }
            .hero { flex-direction: column; align-items: flex-start; padding: 32px 28px; }
            .ring { width: 190px; height: 190px; }
            .ring-text b { font-size: 44px; }
        }

        .panel { padding: 26px 28px; border-radius: 26px; background: var(--card); border: 1px solid var(--line); box-shadow: inset 0 1px 0 rgba(255,255,255,.05); }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid var(--line); }
        .panel-head h2 { display: flex; align-items: center; gap: 12px; font-size: 22px; font-weight: 700; letter-spacing: -.01em; color: #fff; }
        .panel-head a { font-size: 15px; color: #aab0c4; text-decoration: none; }
        .panel-head a:hover { color: #fff; }
        .count-pill { font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600; padding: 4px 12px; border-radius: 99px; color: #7db4ff; background: rgba(59,130,246,.14); border: 1px solid rgba(59,130,246,.25); }

        .work-row { display: flex; align-items: center; gap: 18px; padding: 16px 8px; border-bottom: 1px solid var(--line); }
        .work-row:last-child { border-bottom: 0; }
        .date-tile { width: 62px; padding: 8px 0; flex-shrink: 0; text-align: center; border-radius: 14px; background: rgba(255,255,255,.04); border: 1px solid var(--line); }
        .date-tile small { display: block; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .date-tile b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 26px; font-weight: 800; color: #fff; line-height: 1.1; }
        .work-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 19px; font-weight: 700; color: #fff; text-transform: uppercase; }
        .tag { display: inline-flex; align-items: center; font-size: 14px; padding: 3px 13px; border-radius: 99px; color: #c3c7d8; background: rgba(255,255,255,.04); border: 1px solid var(--line); }
        .tag-high { color: #fca5a5; background: rgba(248,113,113,.12); border-color: rgba(248,113,113,.40); font-weight: 600; }
        .tag-med { color: #fbbf24; background: rgba(251,191,36,.12); border-color: rgba(251,191,36,.40); font-weight: 600; }
        .tag-low { color: #4ade80; background: rgba(74,222,128,.12); border-color: rgba(74,222,128,.40); font-weight: 600; }
        .btn-done { display: inline-flex; align-items: center; gap: 6px; height: 52px; padding: 0 26px; border-radius: 14px; font: inherit; font-size: 17px; font-weight: 600; color: #4ade80; background: rgba(74,222,128,.10); border: 1px solid rgba(74,222,128,.35); cursor: pointer; transition: .2s; }
        .btn-done:hover { background: rgba(74,222,128,.18); }

        /* ---------- Galaxy: twinkling stars, planet (bottom right), moon (top right) ---------- */
        .space { position: fixed; inset: 0; z-index: -1; pointer-events: none; overflow: hidden; }
        .stars { position: absolute; inset: 0; background-repeat: repeat; }
        .stars.s1 {
            background-image:
                radial-gradient(1px 1px at 23px 41px, rgba(255,255,255,.9), transparent),
                radial-gradient(1px 1px at 97px 163px, rgba(255,255,255,.7), transparent),
                radial-gradient(1.2px 1.2px at 211px 87px, rgba(200,215,255,.85), transparent),
                radial-gradient(1px 1px at 151px 249px, rgba(255,255,255,.6), transparent),
                radial-gradient(1px 1px at 281px 197px, rgba(255,255,255,.8), transparent);
            background-size: 320px 320px; opacity: .5;
        }
        .stars.s2 {
            background-image:
                radial-gradient(1px 1px at 61px 23px, rgba(255,255,255,.9), transparent),
                radial-gradient(1.3px 1.3px at 223px 131px, rgba(220,200,255,.9), transparent),
                radial-gradient(1px 1px at 347px 289px, rgba(255,255,255,.7), transparent),
                radial-gradient(1px 1px at 131px 377px, rgba(255,255,255,.8), transparent),
                radial-gradient(1px 1px at 419px 61px, rgba(200,225,255,.8), transparent);
            background-size: 470px 470px; animation: twinkle 5s ease-in-out infinite;
        }
        .stars.s3 {
            background-image:
                radial-gradient(1.4px 1.4px at 83px 97px, rgba(255,255,255,.95), transparent),
                radial-gradient(1px 1px at 301px 211px, rgba(230,205,255,.9), transparent),
                radial-gradient(1.2px 1.2px at 503px 351px, rgba(255,255,255,.85), transparent),
                radial-gradient(1px 1px at 197px 487px, rgba(200,215,255,.8), transparent);
            background-size: 610px 610px; animation: twinkle 7s ease-in-out infinite -2.5s;
        }
        @keyframes twinkle { 0%, 100% { opacity: .2; } 50% { opacity: .7; } }
        .planet {
            position: absolute; right: -130px; bottom: -170px; width: 460px; height: 460px; border-radius: 50%;
            background: radial-gradient(circle at 30% 26%, rgba(200,180,255,.5), rgba(99,72,200,.38) 32%, rgba(45,28,110,.55) 60%, rgba(8,7,24,.94) 100%);
            box-shadow: inset -50px -40px 90px rgba(0,0,0,.7), 0 0 110px rgba(99,72,200,.22);
            opacity: .85; animation: planetFloat 45s ease-in-out infinite alternate;
        }
        .planet::before {
            content: ""; position: absolute; left: -24%; top: 34%; width: 148%; height: 34%; border-radius: 50%;
            border: 2px solid rgba(221,200,255,.28); transform: rotate(-16deg);
        }
        .moon {
            position: absolute; right: 5%; top: -14px; width: 56px; height: 56px; border-radius: 50%;
            background: radial-gradient(circle at 32% 30%, rgba(255,255,255,.7), rgba(190,200,230,.4) 45%, rgba(40,44,80,.6) 100%);
            box-shadow: inset -8px -6px 14px rgba(0,0,0,.55), 0 0 24px rgba(180,200,255,.16);
            opacity: .5;
        }
        @keyframes planetFloat { to { transform: translate(-16px, -12px); } }
        @media (max-width: 640px) {
            .planet { width: 260px; height: 260px; right: -110px; bottom: -110px; }
            .moon { display: none; }
        }

        /* ---------- Theme bridge for older pages ---------- */
        [class*="bg-[#14141f]"] { background-color: var(--card) !important; backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
        [class*="bg-[#1b1b28]"] { background-color: rgba(255,255,255,.035) !important; }
        [class*="border-[#23232f]"] { border-color: var(--line) !important; }
        [class*="border-[#2a2a38]"] { border-color: rgba(190,175,255,.16) !important; }

        .glass:focus-within,
        [class*="bg-[#14141f]"]:focus-within { position: relative; z-index: 15; }

        /* ---------- Phone tweaks ---------- */
        body { overflow-x: clip; }

        @media (max-width: 1023px) {
            input:not([type=checkbox]):not([type=radio]):not([type=range]):not([type=file]),
            select, textarea { font-size: 16px !important; }

            .glass, .stat, .hero, .panel, .stat-strip, [class*="bg-[#14141f]"], .sidebar-glass {
                -webkit-backdrop-filter: none; backdrop-filter: none;
            }
            .sidebar-glass { background: rgba(8,8,20,.97); }
        }

        @media (pointer: coarse) {
            .btn-sm { height: 42px; padding: 0 16px; }
            #toggle-btn { width: 42px; height: 42px; }
        }

        @supports (height: 100dvh) { #sidebar { bottom: auto; height: 100dvh; } }

        :focus-visible { outline: 2px solid #7db4ff; outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }
    </style>
</head>

<body class="text-gray-200 min-h-screen">

    <div class="space" aria-hidden="true"><i class="stars s1"></i><i class="stars s2"></i><i class="stars s3"></i><i class="planet"></i><i class="moon"></i></div>

@php
    // Simple line icons
    $icons = [
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'chart' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
        'tasks' => '<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'note' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'folder' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
        'done' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'flag' => '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
        'list' => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
        'search' => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'layers' => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'menu' => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
    ];

    // Navigation: [label, route name, active pattern, icon]
    // 'home' has no heading. Dashboard is reached from the brand link, as in the design.
    $nav = [
        'Overview' => [
            ['Dashboard', 'dashboard', 'dashboard', 'chart'],
        ],

        'Work' => [
            ['Subjects', 'subjects.index', 'subjects.*', 'book'],
            ['Tasks', 'tasks.index', 'tasks.*', 'tasks'],
        ],

        'Study Space' => [
            ['My Notes', 'notes.index', 'notes.*', 'note'],
            ['My Files', 'study_files.index', 'study_files.*', 'folder'],
        ],

        'Tools' => [
            ['Completed Tasks', 'completed_tasks.index', 'completed_tasks.*', 'done'],
            ['Upcoming Tasks', 'upcoming_tasks.index', 'upcoming_tasks.*', 'clock'],
            ['Priority Tasks', 'priority_tasks.index', 'priority_tasks.*', 'flag'],
        ],
    ];

    $pendingCount = 0;

    if (auth()->check()) {
        $pendingCount = \App\Models\Task::where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->count();
    }
@endphp

@auth

    {{-- Mobile overlay --}}
    <div
        id="sidebarOverlay"
        onclick="toggleSidebar()"
        class="hidden fixed inset-0 bg-black/60 z-30 lg:hidden">
    </div>

    {{-- Sidebar --}}
    <aside
        id="sidebar"
        class="sidebar-glass fixed inset-y-0 left-0 z-40 w-64 border-r flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-200">

        {{-- Brand (links to the dashboard) --}}
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-logo"><span><img src="{{ asset('llogo.png') }}" alt="Classtrack logo"></span></span>
            <span>
                <span class="brand-name">Classtrack</span>
                <span class="brand-sub">Study planner</span>
            </span>
        </a>

        {{-- New Task + Search --}}
        <div class="px-4 pt-4 flex gap-2">
            <a href="{{ route('tasks.index') }}" class="btn-primary flex-1" style="height:42px;font-size:15px">
                + New Task
            </a>

            <a
                href="{{ route('binary_search.index') }}"
                title="Search"
                class="w-[42px] flex items-center justify-center border border-white/10 bg-white/5 rounded-xl text-gray-400 hover:text-white hover:bg-white/10">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    {!! $icons['search'] !!}
                </svg>
            </a>
        </div>

        {{-- Navigation --}}
        <nav id="sidebarNav" class="sidebar-scroll flex-1 overflow-y-auto px-3 pb-4">

            @foreach ($nav as $section => $links)

                <p class="nav-label">{{ $section }}</p>

                @foreach ($links as [$label, $routeName, $pattern, $icon])

                    <a
                        href="{{ route($routeName) }}"
                        {{ request()->routeIs($pattern) ? 'data-active=1' : '' }}
                        class="nav-link {{ request()->routeIs($pattern) ? 'is-active' : '' }}">

                        <svg fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            {!! $icons[$icon] !!}
                        </svg>

                        <span class="flex-1">{{ $label }}</span>

                        @if ($label === 'Tasks' && $pendingCount > 0)
                            <span class="text-[11px] font-semibold bg-blue-600 text-white rounded-md px-1.5 py-0.5">
                                {{ $pendingCount }}
                            </span>
                        @endif

                    </a>

                @endforeach

            @endforeach

        </nav>

        {{-- Keeps the sidebar scroll position between pages --}}
        <script>
            (function () {
                var nav = document.getElementById('sidebarNav');
                if (!nav) return;

                var KEY = 'sidebarScrollTop';
                var saved = null;

                try { saved = sessionStorage.getItem(KEY); } catch (e) {}

                if (saved !== null) {
                    nav.scrollTop = parseInt(saved, 10) || 0;
                } else {
                    var active = nav.querySelector('a[data-active]');
                    if (active) active.scrollIntoView({ block: 'nearest' });
                }

                nav.addEventListener('scroll', function () {
                    try { sessionStorage.setItem(KEY, nav.scrollTop); } catch (e) {}
                }, { passive: true });
            })();
        </script>

        {{-- User card --}}
        <div class="user-card">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>

            <a href="{{ route('account.index') }}" class="flex-1 min-w-0">
                <span class="block text-white text-sm font-semibold truncate uppercase">{{ auth()->user()->name }}</span>
                <span class="block text-gray-500 text-xs">Account settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Log out" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        {!! $icons['logout'] !!}
                    </svg>
                </button>
            </form>
        </div>

    </aside>

    {{-- Content area --}}
    <div class="lg:pl-64 min-h-screen">

        {{-- Mobile top bar --}}
        <div class="sidebar-glass sticky top-0 z-20 lg:hidden flex items-center gap-3 px-4 py-3 border-b">
            <button type="button" onclick="toggleSidebar()" aria-label="Open menu" class="p-2 -ml-2 text-gray-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    {!! $icons['menu'] !!}
                </svg>
            </button>
            <span class="text-white font-semibold">Classtrack</span>
        </div>

        <main>
            @yield('content')
        </main>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');

            sidebar.classList.toggle('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.toggle('hidden');

            const open = !sidebar.classList.contains('-translate-x-full');
            document.body.style.overflow = open ? 'hidden' : '';
        }

        document.addEventListener('keydown', function (e) {
            const sidebar = document.getElementById('sidebar');
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }
        });
    </script>

@else

    {{-- Guests (login / register pages) get no sidebar --}}
    <main>
        @yield('content')
    </main>

@endauth

@stack('scripts')

</body>
</html>

