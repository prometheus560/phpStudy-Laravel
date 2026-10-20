<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') - Student Study Planner</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --card: rgba(16,14,32,.72);
            --line: rgba(190,175,255,.11);
            --field: #100f1d;
            --field-line: #2a2842;
            --muted: #8b90a5;
        }

        html { color-scheme: dark; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            background:
                radial-gradient(45rem 32rem at 8% -8%, rgba(99,102,241,.20), transparent 60%),
                radial-gradient(40rem 30rem at 105% 12%, rgba(168,85,247,.14), transparent 60%),
                radial-gradient(50rem 36rem at 100% 108%, rgba(37,99,235,.16), transparent 60%),
                radial-gradient(34rem 24rem at -4% 100%, rgba(192,38,211,.07), transparent 60%),
                #05060d;
            background-attachment: fixed;
        }

        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(167,139,250,.28); border-radius: 3px; }

        /* ---------- Shared "login style" components ---------- */
        .sidebar-glass { background: rgba(10,9,24,.74); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-color: var(--line); }

        .glass { background: var(--card); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--line); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05); }
        .glass-accent { border-color: rgba(129,140,248,.34); box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 60px rgba(59,130,246,.08), inset 0 1px 0 rgba(255,255,255,.06); }

        .page-title { font-size: 30px; font-weight: 700; letter-spacing: -.02em; color: #fff; }
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

        .btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 48px; padding: 0 20px; border: 0; border-radius: 12px; font: inherit; font-size: 15px; font-weight: 600; color: #fff; background: linear-gradient(135deg, #3b82f6, #4f46e5); box-shadow: 0 10px 28px rgba(59,130,246,.35); cursor: pointer; text-decoration: none; transition: transform .15s, box-shadow .2s; }
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

        /* ---------- Dashboard extras ---------- */
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


        /* ---------- Galaxy touch: faint twinkling stars ---------- */
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
        /* ---------- A small planet and moon (kept faint, in the corners) ---------- */
        .planet {
            position: absolute; right: -110px; bottom: -150px; width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle at 30% 26%, rgba(216,196,255,.6), rgba(139,92,246,.4) 32%, rgba(67,38,140,.55) 60%, rgba(12,9,32,.92) 100%);
            box-shadow: inset -50px -40px 90px rgba(0,0,0,.7), 0 0 100px rgba(139,92,246,.2);
            opacity: .8; animation: planetFloat 45s ease-in-out infinite alternate;
        }
        .planet::before {
            content: ""; position: absolute; left: -24%; top: 34%; width: 148%; height: 34%; border-radius: 50%;
            border: 2px solid rgba(221,200,255,.3); transform: rotate(-16deg);
        }
        .moon {
            position: absolute; right: 5%; top: 3%; width: 42px; height: 42px; border-radius: 50%;
            background: radial-gradient(circle at 32% 30%, rgba(255,255,255,.75), rgba(190,200,230,.45) 45%, rgba(40,44,80,.6) 100%);
            box-shadow: inset -8px -6px 14px rgba(0,0,0,.55), 0 0 24px rgba(180,200,255,.18);
            opacity: .45;
        }
        @keyframes planetFloat { to { transform: translate(-16px, -12px); } }
        @media (max-width: 640px) {
            .planet { width: 260px; height: 260px; right: -110px; bottom: -110px; }
            .moon { display: none; }
        }


        /* ---------- Theme bridge: older pages still use fixed dark colours.
                      These rules give them the same glass / violet look. ---------- */
        [class*="bg-[#14141f]"] { background-color: var(--card) !important; backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
        [class*="bg-[#1b1b28]"] { background-color: rgba(255,255,255,.035) !important; }
        [class*="border-[#23232f]"] { border-color: var(--line) !important; }
        [class*="border-[#2a2a38]"] { border-color: rgba(190,175,255,.16) !important; }

        /* Search pop-ups: the card that holds the focused field rises above the cards below it,
           so a dropdown is never hidden behind the next card. */
        .glass:focus-within,
        [class*="bg-[#14141f]"]:focus-within { position: relative; z-index: 15; }


        /* ================= SIDEBAR UPGRADE ================= */

        /* Brand: logo with a slow orbiting ring and a tiny moon */
        .brand-mark { position: relative; width: 40px; height: 40px; flex: none; }
        .brand-mark .orbit { position: absolute; inset: -6px; border-radius: 50%; border: 1px solid rgba(167,139,250,.3); animation: ctSpin 16s linear infinite; }
        .brand-mark .orbit::after { content: ""; position: absolute; top: -3px; left: 50%; width: 6px; height: 6px; margin-left: -3px; border-radius: 50%; background: #c4b5fd; box-shadow: 0 0 10px #a78bfa; }
        @keyframes ctSpin { to { transform: rotate(360deg); } }
        .brand-name { display: block; color: #fff; font-weight: 700; font-size: 15px; letter-spacing: -.02em; line-height: 1.1; }
        .brand-sub { display: block; font-size: 11px; color: #7d8299; margin-top: 3px; }

        /* New Task button with a shine on hover */
        .nt-btn { position: relative; overflow: hidden; }
        .nt-btn::after { content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255,255,255,.3), transparent); transform: skewX(-20deg); transition: left .6s; }
        .nt-btn:hover::after { left: 120%; }

        .sr-btn kbd { display: none; }

        /* Section headings: a small star and a fading rule */
        .sec { display: flex; align-items: center; gap: 8px; padding: 14px 12px 8px; font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #6f748c; }
        .sec::before { content: "\2726"; font-size: 8px; color: #8b7be0; }
        .sec::after { content: ""; flex: 1; height: 1px; background: linear-gradient(90deg, rgba(167,139,250,.28), transparent); }

        /* Links */
        #sidebarNav { position: relative; }
        .nv { position: relative; display: flex; align-items: center; gap: 12px; padding: 9px 12px; margin-bottom: 2px; border-radius: 12px; font-size: 14px; color: #9aa0b8; text-decoration: none; transition: color .2s, background .2s, transform .2s; }
        .nv svg { width: 18px; height: 18px; flex: none; transition: color .2s, transform .25s; }
        .nv:hover { color: #fff; background: rgba(255,255,255,.04); }
        .nv:hover svg { transform: scale(1.12); }
        .nv.on { color: #e4dcff; font-weight: 500; background: linear-gradient(90deg, rgba(139,92,246,.22), rgba(139,92,246,.04) 80%); }
        .nv.on svg { color: #c4b5fd; }
        .nv[data-tint="blue"]:hover svg   { color: #60a5fa; }
        .nv[data-tint="violet"]:hover svg { color: #a78bfa; }
        .nv[data-tint="green"]:hover svg  { color: #4ade80; }
        .nv[data-tint="amber"]:hover svg  { color: #fbbf24; }
        .nv[data-tint="rose"]:hover svg   { color: #fb7185; }

        /* Glowing marker that slides to the current page */
        #navMarker { position: absolute; left: 0; top: 0; width: 3px; height: 0; border-radius: 0 4px 4px 0; background: linear-gradient(#60a5fa, #a78bfa); box-shadow: 0 0 14px 1px rgba(139,92,246,.9); pointer-events: none; opacity: 0; transition: transform .45s cubic-bezier(.3,.9,.3,1), height .3s, opacity .2s; }

        .nv-badge { font-size: 11px; font-weight: 600; background: #2563eb; color: #fff; border-radius: 8px; padding: 1px 7px; }
        .nv-late { width: 7px; height: 7px; border-radius: 50%; background: #fb7185; box-shadow: 0 0 8px #fb7185; margin-right: 2px; }

        /* Account: avatar wrapped in your overall-progress ring */
        .av-ring { width: 40px; height: 40px; border-radius: 50%; padding: 2px; flex: none; display: grid; place-items: center; }
        .av-ring > span { width: 100%; height: 100%; border-radius: 50%; display: grid; place-items: center; background: #14122a; color: #bcd4ff; font-weight: 600; font-size: 14px; }

        /* ---------- Phone dock ---------- */
        .dock { position: fixed; left: 10px; right: 10px; bottom: calc(10px + env(safe-area-inset-bottom, 0px)); z-index: 25; height: 62px; display: grid; grid-template-columns: 1fr 1fr 72px 1fr 1fr; align-items: center; border-radius: 22px; border: 1px solid var(--line); background: rgba(12,10,28,.94); box-shadow: 0 18px 50px rgba(0,0,0,.6), inset 0 1px 0 rgba(255,255,255,.05); }
        .dock a, .dock button { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; height: 100%; color: #858ba3; font: inherit; font-size: 10.5px; background: none; border: 0; text-decoration: none; cursor: pointer; }
        .dock svg { width: 21px; height: 21px; }
        .dock .on { color: #c4b5fd; }
        .dock .on::before { content: ""; position: absolute; top: 0; width: 22px; height: 3px; border-radius: 0 0 4px 4px; background: linear-gradient(90deg, #60a5fa, #a78bfa); box-shadow: 0 0 10px rgba(139,92,246,.9); }
        .dock .fab { width: 56px; height: 56px; margin: -26px auto 0; border-radius: 50%; color: #fff; background: linear-gradient(135deg, #3b82f6, #6d5cf0); box-shadow: 0 10px 28px rgba(79,70,229,.6), 0 0 0 5px #070614; height: 56px; }
        .dock .fab svg { width: 26px; height: 26px; }
        .dock .dot { position: absolute; top: 9px; right: calc(50% - 16px); min-width: 15px; height: 15px; padding: 0 4px; border-radius: 8px; background: #2563eb; color: #fff; font-size: 9.5px; font-weight: 700; display: grid; place-items: center; }

        @media (max-width: 1023px) {
            main { padding-bottom: calc(86px + env(safe-area-inset-bottom, 0px)); }
        }


        /* ---------- Phone tweaks ---------- */
        body { overflow-x: clip; }                       /* never scroll sideways */

        @media (max-width: 1023px) {
            /* iPhone Safari zooms into any field with text smaller than 16px */
            input:not([type=checkbox]):not([type=radio]):not([type=range]):not([type=file]),
            select, textarea { font-size: 16px !important; }

            /* Frosted blur is heavy for phone graphics chips: keep the look, drop the blur */
            .glass, .stat, [class*="bg-[#14141f]"], .sidebar-glass {
                -webkit-backdrop-filter: none; backdrop-filter: none;
            }
            .sidebar-glass { background: rgba(10,9,24,.97); }
        }

        @media (min-width: 1024px) {
            .sr-btn kbd { display: block; position: absolute; bottom: -2px; right: -2px; }
        }

        /* Bigger touch targets on touch screens */
        @media (pointer: coarse) {
            .btn-sm { height: 42px; padding: 0 16px; }
            #toggle-btn { width: 42px; height: 42px; }
        }

        /* The drawer fits the visible screen even when the phone's address bar shows or hides */
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
        'plus' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
    ];

    // Navigation
    // [label, route name, active pattern, icon, hover tint]
    $nav = [
        'home' => [
            ['Dashboard', 'dashboard', 'dashboard', 'chart', 'violet'],
        ],

        'Work' => [
            ['Subjects', 'subjects.index', 'subjects.*', 'book', 'blue'],
            ['Tasks', 'tasks.index', 'tasks.*', 'tasks', 'violet'],
            ['Schedule', 'schedule.index', 'schedule.*', 'calendar', 'blue'],
        ],

        'Study Space' => [
            ['My Notes', 'notes.index', 'notes.*', 'note', 'amber'],
            ['My Files', 'study_files.index', 'study_files.*', 'folder', 'amber'],
        ],

        'Tools' => [
            ['Completed Tasks', 'completed_tasks.index', 'completed_tasks.*', 'done', 'green'],
            ['Upcoming Tasks', 'upcoming_tasks.index', 'upcoming_tasks.*', 'clock', 'blue'],
            ['Priority Tasks', 'priority_tasks.index', 'priority_tasks.*', 'flag', 'rose'],
        ],
    ];

    $pendingCount = 0;
    $totalCount   = 0;
    $overdueCount = 0;

    if (auth()->check()) {
        $pendingCount = \App\Models\Task::where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->count();

        $totalCount = \App\Models\Task::where('user_id', auth()->id())->count();

        $overdueCount = \App\Models\Task::where('user_id', auth()->id())
            ->where('status', 'Pending')
            ->whereDate('deadline', '<', today())
            ->count();
    }

    // Overall progress for the avatar ring
    $doneCount = max($totalCount - $pendingCount, 0);
    $ringPct   = $totalCount > 0 ? (int) round($doneCount / $totalCount * 100) : 0;
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

        {{-- Brand --}}
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-4 px-5 py-5 border-b border-white/10">

            <span class="brand-mark">
                <i class="orbit"></i>
                <span class="w-10 h-10 rounded-xl bg-white overflow-hidden flex items-center justify-center shadow-lg shadow-violet-500/30 ring-1 ring-white/10">
                    <img src="{{ asset('llogo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </span>
            </span>

            <span class="block">
                <span class="brand-name">Classtrack</span>
                <span class="brand-sub">Study planner</span>
            </span>
        </a>

        {{-- New Task + Search --}}
        <div class="px-4 pt-4 flex gap-2">

            <a
                href="{{ route('tasks.index') }}"
                class="btn-primary nt-btn flex-1"
                style="height:40px;font-size:14px">

                + New Task
            </a>

            <a
                href="{{ route('binary_search.index') }}"
                id="searchLink"
                title="Search (Ctrl+K)"
                class="sr-btn relative w-10 flex items-center justify-center border border-white/10 bg-white/5 rounded-xl text-gray-400 hover:text-white hover:bg-white/10">

                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24">

                    {!! $icons['search'] !!}

                </svg>
            </a>
        </div>

        {{-- Navigation (id added so the scroll position can be remembered) --}}
        <nav id="sidebarNav" class="sidebar-scroll flex-1 overflow-y-auto px-3 py-2">

            <i id="navMarker"></i>

            @foreach ($nav as $section => $links)

                <p class="sec">{{ $section }}</p>

                @foreach ($links as [$label, $routeName, $pattern, $icon, $tint])

                    <a
                        href="{{ route($routeName) }}"
                        data-tint="{{ $tint }}"
                        {{ request()->routeIs($pattern) ? 'data-active=1' : '' }}
                        class="nv {{ request()->routeIs($pattern) ? 'on' : '' }}">

                        <svg
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            viewBox="0 0 24 24">

                            {!! $icons[$icon] !!}

                        </svg>

                        <span class="flex-1">
                            {{ $label }}
                        </span>

                        {{-- Overdue dot + pending tasks badge --}}
                        @if ($label === 'Tasks')
                            @if ($overdueCount > 0)
                                <span class="nv-late" title="{{ $overdueCount }} overdue"></span>
                            @endif
                            @if ($pendingCount > 0)
                                <span class="nv-badge">{{ $pendingCount }}</span>
                            @endif
                        @endif

                    </a>

                @endforeach

            @endforeach

        </nav>

        {{-- Keeps the sidebar scroll position between pages, and slides the glowing
             marker from the previous page's spot to the current one. --}}
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
                    // First visit: make sure the current page's menu item is visible
                    var active = nav.querySelector('a[data-active]');
                    if (active) active.scrollIntoView({ block: 'nearest' });
                }

                nav.addEventListener('scroll', function () {
                    try { sessionStorage.setItem(KEY, nav.scrollTop); } catch (e) {}
                }, { passive: true });

                // Sliding marker
                var marker = document.getElementById('navMarker');
                var cur = nav.querySelector('a[data-active]');
                if (marker && cur) {
                    var h = Math.round(cur.offsetHeight * 0.6);
                    var to = cur.offsetTop + Math.round((cur.offsetHeight - h) / 2);
                    var from = null;
                    try { from = sessionStorage.getItem('navMarkerTop'); } catch (e) {}

                    marker.style.height = h + 'px';
                    marker.style.transition = 'none';
                    marker.style.transform = 'translateY(' + (from !== null ? from : to) + 'px)';
                    marker.style.opacity = '1';
                    marker.offsetHeight; // apply the start position before animating
                    marker.style.transition = '';
                    requestAnimationFrame(function () {
                        marker.style.transform = 'translateY(' + to + 'px)';
                    });

                    try { sessionStorage.setItem('navMarkerTop', to); } catch (e) {}
                }
            })();
        </script>

        {{-- User card --}}
        <div class="border-t border-white/10 p-3">

            <div class="flex items-center gap-3 px-2 py-2">

                <span class="av-ring" title="Overall progress: {{ $ringPct }}%"
                      style="background: conic-gradient(#6366f1 {{ $ringPct }}%, rgba(255,255,255,.1) 0)">
                    <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </span>

                <a
                    href="{{ route('account.index') }}"
                    class="flex-1 min-w-0">

                    <span class="block text-white text-sm font-medium truncate">
                        {{ auth()->user()->name }}
                    </span>

                    <span class="block text-gray-500 text-xs">
                        Account settings
                    </span>

                </a>

                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        title="Logout"
                        class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5">

                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            viewBox="0 0 24 24">

                            {!! $icons['logout'] !!}

                        </svg>

                    </button>

                </form>

            </div>

        </div>

    </aside>

    {{-- Content area --}}
    <div class="lg:pl-64 min-h-screen">

        {{-- Mobile top bar --}}
        <div class="sidebar-glass sticky top-0 z-20 lg:hidden flex items-center gap-3 px-4 py-3 border-b">

            <button
                type="button"
                onclick="toggleSidebar()"
                aria-label="Open menu"
                class="p-2 -ml-2 text-gray-300 hover:text-white">

                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    viewBox="0 0 24 24">

                    {!! $icons['menu'] !!}

                </svg>

            </button>

            <span class="text-white font-semibold">
                Classtrack
            </span>

        </div>

        <main>
            @yield('content')
        </main>

    </div>

    {{-- Phone dock: the main pages one tap away, the full menu under "More" --}}
    <nav class="dock lg:hidden" aria-label="Quick navigation">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons['chart'] !!}</svg>Home
        </a>
        <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.*') ? 'on' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons['tasks'] !!}</svg>Tasks
            @if ($pendingCount > 0)<span class="dot">{{ $pendingCount }}</span>@endif
        </a>
        <a href="{{ route('tasks.index') }}" class="fab" aria-label="New task">
            <svg fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons['plus'] !!}</svg>
        </a>
        <a href="{{ route('schedule.index') }}" class="{{ request()->routeIs('schedule.*') ? 'on' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons['calendar'] !!}</svg>Schedule
        </a>
        <button type="button" onclick="toggleSidebar()" aria-label="Open full menu">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">{!! $icons['menu'] !!}</svg>More
        </button>
    </nav>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');

            sidebar.classList.toggle('-translate-x-full');
            document.getElementById('sidebarOverlay').classList.toggle('hidden');

            // While the menu is open, the page behind it must not scroll
            const open = !sidebar.classList.contains('-translate-x-full');
            document.body.style.overflow = open ? 'hidden' : '';
        }

        document.addEventListener('keydown', function (e) {
            const sidebar = document.getElementById('sidebar');
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                toggleSidebar();
            }

            // Ctrl+K (or Cmd+K) opens Search from any page
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                var s = document.getElementById('searchLink');
                if (s) { e.preventDefault(); window.location.href = s.href; }
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