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

    {{-- Top navbar --}}
    <nav class="bg-[#14141f] border-b border-[#23232f] px-6 py-3 flex items-center justify-between relative z-20">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
              
                <span class="text-white font-semibold text-lg">Student Study Planner</span>
            </a>

            <div class="flex items-center gap-1 text-sm">
                <a href="{{ route('home') }}"
                   class="px-3 py-2 rounded-lg {{ request()->routeIs('home') ? 'bg-blue-600/15 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Home
                </a>
                <a href="{{ route('subjects.index') }}"
                   class="px-3 py-2 rounded-lg {{ request()->routeIs('subjects.*') ? 'bg-blue-600/15 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Subjects
                </a>
                <a href="{{ route('tasks.index') }}"
                   class="px-3 py-2 rounded-lg {{ request()->routeIs('tasks.*') ? 'bg-blue-600/15 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Tasks
                </a>
                <a href="{{ route('schedule.index') }}"
                   class="px-3 py-2 rounded-lg {{ request()->routeIs('schedule.*') ? 'bg-blue-600/15 text-blue-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    Schedule
                </a>

                {{-- Study Space dropdown --}}
                <div class="relative">
                    <button type="button" onclick="toggleDropdown(this, 'studySpaceMenu')"
                            class="px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 flex items-center gap-1">
                        Study Space <span class="text-xs">▾</span>
                    </button>
                    <div id="studySpaceMenu" class="dropdown-menu hidden absolute left-0 top-full pt-2 w-44 z-30">
                        <div class="bg-[#14141f] border border-[#23232f] rounded-lg py-1 shadow-lg">
                            <a href="{{ route('notes.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Notes</a>
                            <a href="{{ route('study_files.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white">My Files</a>
                        </div>
                    </div>
                </div>

                {{-- Tools dropdown (DSA) --}}
                <div class="relative">
                    <button type="button" onclick="toggleDropdown(this, 'toolsMenu')"
                            class="px-3 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 flex items-center gap-1">
                        Tools <span class="text-xs">▾</span>
                    </button>
                    <div id="toolsMenu" class="dropdown-menu hidden absolute left-0 top-full pt-2 w-48 z-30">
                        <div class="bg-[#14141f] border border-[#23232f] rounded-lg py-1 shadow-lg">
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

        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-white">Account</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white">Logout</button>
            </form>
        </div>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>

    <script>
        function toggleDropdown(button, menuId) {
            const menu = document.getElementById(menuId);
            const isOpen = !menu.classList.contains('hidden');

            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));  
            if (!isOpen) {
                menu.classList.remove('hidden');
            }
        }
        document.addEventListener('click', function (event) {
            const clickedInsideDropdown = event.target.closest('.relative');
            if (!clickedInsideDropdown) {
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

</body>
</html>