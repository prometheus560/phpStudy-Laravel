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
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: navy;
            border-radius: 3px;
        }
    </style>
</head>

<body class="bg-[#0a0a12] text-gray-200 min-h-screen">

@php
    // Simple line icons (24x24)
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

    /*
     * Pending task count for the currently logged-in user.
     *
     * IMPORTANT:
     * We use tasks.user_id directly so the badge matches
     * the same user ownership used by TaskController.
     */
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
        class="fixed inset-y-0 left-0 z-40 w-64 bg-[#0f0f1a] border-r border-[#23232f] flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-200">

        {{-- Brand --}}
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-5 py-5 border-b border-[#23232f]">

            <span>
                <span class="block text-white font-semibold text-sm leading-tight">
                    Student Study Planner
                </span>

                <span class="block text-gray-500 text-xs">
                    Study Dashboard
                </span>
            </span>
        </a>

        {{-- New Task + Search --}}
        <div class="px-4 pt-4 flex gap-2">

            <a
                href="{{ route('tasks.index') }}"
                class="flex-1 text-center bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-3 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">

                + New Task
            </a>

            <a
                href="{{ route('binary_search.index') }}"
                title="Search"
                class="w-10 flex items-center justify-center border border-[#2a2a38] bg-[#14141f] rounded-lg text-gray-400 hover:text-white hover:bg-white/5">

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
                        class="flex items-center gap-3 px-3 py-2 mb-0.5 rounded-lg text-sm {{ request()->routeIs($pattern) ? 'bg-blue-600/15 text-blue-300 font-medium' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">

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

        {{-- NEW: keeps the sidebar scroll position between pages.
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
        <div class="border-t border-[#23232f] p-3">

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
        <div class="lg:hidden flex items-center gap-3 px-4 py-3 bg-[#0f0f1a] border-b border-[#23232f]">

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