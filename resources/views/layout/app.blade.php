<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Student Study Planner</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --card: rgba(20,20,31,.78);
            --line: rgba(255,255,255,.08);
            --field: #12121c;
            --field-line: #262636;
            --muted: #8b90a5;
        }

        html { color-scheme: dark; }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            background:
                radial-gradient(60rem 40rem at 8% -10%, rgba(59,130,246,.20), transparent 60%),
                radial-gradient(50rem 40rem at 110% 110%, rgba(99,102,241,.16), transparent 60%),
                #07070d;
            background-attachment: fixed;
        }

        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 3px; }

        /* ---------- Shared "login style" components ---------- */
        .sidebar-glass { background: rgba(12,12,22,.72); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-color: var(--line); }

        .glass { background: var(--card); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--line); border-radius: 20px; box-shadow: 0 30px 80px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.05); }
        .glass-accent { border-color: rgba(59,130,246,.28); box-shadow: 0 30px 80px rgba(0,0,0,.45), 0 0 60px rgba(59,130,246,.08), inset 0 1px 0 rgba(255,255,255,.06); }

        .page-title { font-size: 30px; font-weight: 700; letter-spacing: -.02em; color: #fff; }
        .page-sub { color: var(--muted); font-size: 14px; margin-top: 4px; }
        .grad-text { background: linear-gradient(90deg, #60a5fa, #818cf8); -webkit-background-clip: text; background-clip: text; color: transparent; }

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

        :focus-visible { outline: 2px solid #7db4ff; outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }
    </style>
</head>

<body class="text-gray-200 min-h-screen">

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

    // Navigation
    // [label, route name, active pattern, icon]
    $nav = [
        'home' => [
            ['Dashboard', 'dashboard', 'dashboard', 'chart'],
        ],

        'Work' => [
            ['Subjects', 'subjects.index', 'subjects.*', 'book'],
            ['Tasks', 'tasks.index', 'tasks.*', 'tasks'],
            ['Schedule', 'schedule.index', 'schedule.*', 'calendar'],
        ],

        'Study Space' => [
            ['My Notes', 'notes.index', 'notes.*', 'note'],
            ['My Files', 'study_files.index', 'study_files.*', 'folder'],
        ],

        'Tools' => [
            ['Completed Tasks', 'completed_tasks.index', 'completed_tasks.*', 'done'],
            ['Upcoming Tasks', 'upcoming_tasks.index', 'upcoming_tasks.*', 'clock'],
            ['Priority Tasks', 'priority_tasks.index', 'priority_tasks.*', 'flag'],
            ['Sorted Tasks', 'sorted_tasks.index', 'sorted_tasks.*', 'list'],
            ['Selection Sort', 'selection_sort.index', 'selection_sort.*', 'layers'],
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

        {{-- Brand --}}
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-5 py-5 border-b border-white/10">

            <span class="w-10 h-10 rounded-xl bg-white overflow-hidden flex items-center justify-center shadow-lg shadow-blue-500/20 ring-1 ring-white/10 shrink-0">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-contain">
            </span>

            <span class="block text-white font-semibold text-sm leading-tight">
                Student Study Planner
            </span>
        </a>

        {{-- New Task + Search --}}
        <div class="px-4 pt-4 flex gap-2">

            <a
                href="{{ route('tasks.index') }}"
                class="btn-primary flex-1"
                style="height:40px;font-size:14px">

                + New Task
            </a>

            <a
                href="{{ route('binary_search.index') }}"
                title="Search"
                class="w-10 flex items-center justify-center border border-white/10 bg-white/5 rounded-xl text-gray-400 hover:text-white hover:bg-white/10">

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
        <nav id="sidebarNav" class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4">

            @foreach ($nav as $section => $links)

                <p class="px-3 pt-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    {{ $section }}
                </p>

                @foreach ($links as [$label, $routeName, $pattern, $icon])

                    <a
                        href="{{ route($routeName) }}"
                        {{ request()->routeIs($pattern) ? 'data-active=1' : '' }}
                        class="flex items-center gap-3 px-3 py-2 mb-0.5 rounded-xl border text-sm {{ request()->routeIs($pattern) ? 'bg-blue-500/10 border-blue-500/25 text-[#a9cdff] font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">

                        <svg
                            class="w-[18px] h-[18px] shrink-0"
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

                        {{-- Pending Tasks Badge --}}
                        @if ($label === 'Tasks' && $pendingCount > 0)

                            <span class="text-[11px] font-semibold bg-blue-600 text-white rounded-md px-1.5 py-0.5">
                                {{ $pendingCount }}
                            </span>

                        @endif

                    </a>

                @endforeach

            @endforeach

        </nav>

        {{-- Keeps the sidebar scroll position between pages.
             Runs right after the menu is drawn, so there is no jump. --}}
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
            })();
        </script>

        {{-- User card --}}
        <div class="border-t border-white/10 p-3">

            <div class="flex items-center gap-3 px-2 py-2">

                <span class="w-9 h-9 rounded-full bg-blue-600/20 border border-blue-500/30 text-blue-300 flex items-center justify-center font-semibold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
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
        <div class="sidebar-glass lg:hidden flex items-center gap-3 px-4 py-3 border-b">

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
                Student Study Planner
            </span>

        </div>

        <main>
            @yield('content')
        </main>

    </div>

    <script>
        function toggleSidebar() {
            document
                .getElementById('sidebar')
                .classList
                .toggle('-translate-x-full');

            document
                .getElementById('sidebarOverlay')
                .classList
                .toggle('hidden');
        }
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