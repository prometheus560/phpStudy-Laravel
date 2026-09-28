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
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0a0a12] text-gray-200 min-h-screen">

    <header class="bg-[#0f0f1a] border-b border-[#23232f] relative z-20">

        {{-- Top bar --}}
        <nav class="px-4 sm:px-6 py-3 flex items-center justify-between">

            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded-md flex items-center justify-center text-black font-bold text-sm shrink-0">S</div>
                    <span class="text-white font-semibold text-base sm:text-lg">Student Study Planner</span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden lg:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-lg {{ request()->routeIs('home') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Home</a>
                    <a href="{{ route('subjects.index') }}"
                       class="px-3 py-2 rounded-lg {{ request()->routeIs('subjects.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Subjects</a>
                    <a href="{{ route('tasks.index') }}"
                       class="px-3 py-2 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Tasks</a>
                    <a href="{{ route('schedule.index') }}"
                       class="px-3 py-2 rounded-lg {{ request()->routeIs('schedule.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">Schedule</a>

                    {{-- Study Space dropdown --}}
                    <div class="relative js-dropdown">
                        <button type="button" onclick="toggleDropdown('studySpaceMenu')"
                                class="px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 flex items-center gap-1">
                            Study Space <span class="text-xs">▾</span>
                        </button>
                        <div id="studySpaceMenu" class="dropdown-menu hidden absolute left-0 top-full pt-2 w-44 z-30">
                            <div class="bg-[#0f0f1a] border border-[#23232f] rounded-lg py-1 shadow-lg">
                                <a href="{{ route('notes.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Notes</a>
                                <a href="{{ route('study_files.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Files</a>
                            </div>
                        </div>
                    </div>

                    {{-- Tools dropdown --}}
                    <div class="relative js-dropdown">
                        <button type="button" onclick="toggleDropdown('toolsMenu')"
                                class="px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 flex items-center gap-1">
                            Tools <span class="text-xs">▾</span>
                        </button>
                        <div id="toolsMenu" class="dropdown-menu hidden absolute left-0 top-full pt-2 w-48 z-30">
                            <div class="bg-[#0f0f1a] border border-[#23232f] rounded-lg py-1 shadow-lg">
                                <a href="{{ route('completed_tasks.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Completed Tasks</a>
                                <a href="{{ route('upcoming_tasks.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Upcoming Tasks</a>
                                <a href="{{ route('priority_tasks.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Priority Tasks</a>
                                <a href="{{ route('sorted_tasks.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Sorted Tasks</a>
                                <a href="{{ route('binary_search.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Binary Search</a>
                                <a href="{{ route('selection_sort.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">Selection Sort</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Desktop account / logout --}}
            <div class="hidden lg:flex items-center gap-4 text-sm">
                <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-white">Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white">Logout</button>
                </form>
            </div>

            {{-- Mobile hamburger --}}
            <button type="button" id="mobileMenuButton" aria-label="Toggle menu"
                    onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
                    class="lg:hidden text-gray-300 hover:text-white p-2 -mr-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </nav>

        {{-- Mobile menu --}}
        <div id="mobileMenu" class="hidden lg:hidden border-t border-[#23232f] px-4 py-3 text-sm">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg {{ request()->routeIs('home') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5' }}">Home</a>
            <a href="{{ route('subjects.index') }}" class="block px-3 py-2.5 rounded-lg {{ request()->routeIs('subjects.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5' }}">Subjects</a>
            <a href="{{ route('tasks.index') }}" class="block px-3 py-2.5 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5' }}">Tasks</a>
            <a href="{{ route('schedule.index') }}" class="block px-3 py-2.5 rounded-lg {{ request()->routeIs('schedule.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5' }}">Schedule</a>

            <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-gray-500">Study Space</p>
            <a href="{{ route('notes.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">My Notes</a>
            <a href="{{ route('study_files.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">My Files</a>

            <p class="px-3 pt-4 pb-1 text-xs uppercase tracking-wide text-gray-500">Tools</p>
            <a href="{{ route('completed_tasks.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Completed Tasks</a>
            <a href="{{ route('upcoming_tasks.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Upcoming Tasks</a>
            <a href="{{ route('priority_tasks.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Priority Tasks</a>
            <a href="{{ route('sorted_tasks.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Sorted Tasks</a>
            <a href="{{ route('binary_search.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Binary Search</a>
            <a href="{{ route('selection_sort.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Selection Sort</a>

            <div class="border-t border-[#23232f] mt-3 pt-3">
                <a href="{{ route('account.index') }}" class="block px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">Logout</button>
                </form>
            </div>
        </div>

    </header>

    <main>
        @yield('content')
    </main>

    <script>
        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            const wasOpen = !menu.classList.contains('hidden');

            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));

            if (!wasOpen) {
                menu.classList.remove('hidden');
            }
        }

        // Close desktop dropdowns when clicking anywhere outside them
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.js-dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

</body>
</html>