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
