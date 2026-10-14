@extends('layouts.app')

@section('title', 'Upcoming Tasks')

@section('content')
    @php
        $total    = count($tasks);
        $nextTask = $total > 0 ? $tasks[0] : null;
    @endphp

    <div class="mx-auto max-w-6xl px-6 py-8">

 
        <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-3xl font-bold tracking-tight text-white">
                        Upcoming Tasks
                    </h2>

                    <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        Queue · FIFO
                    </span>
                </div>

                <p class="mt-2 text-sm text-gray-400">
                    Stay organized and keep track of what needs your attention next.
                </p>
            </div>

            <a href="{{ route('tasks.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2.5 text-sm font-semibold text-gray-300 transition hover:border-white/20 hover:bg-white/[0.08] hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                     aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                    <path d="M20 12H9" />
                </svg>
                Back to Tasks
            </a>
        </header>

      
        <section class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- Pending count --}}
            <article class="relative overflow-hidden rounded-2xl border border-blue-400/15 bg-[#10101c] p-5">
                <div class="absolute -right-8 -top-10 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl"></div>

                <div class="relative flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-blue-400/20 bg-blue-500/10 text-blue-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                             aria-hidden="true">
                            <rect x="3" y="4" width="18" height="17" rx="2" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                            <path d="m9 15 2 2 4-4" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-2xl font-bold leading-none text-white">
                            {{ $total }}
                        </p>
                        <p class="mt-1.5 text-sm text-gray-400">
                            {{ $total === 1 ? 'Pending task' : 'Pending tasks' }}
                        </p>
                    </div>
                </div>
            </article>

            {{-- Next task --}}
            <article class="relative overflow-hidden rounded-2xl border border-indigo-400/15 bg-[#10101c] p-5">
                <div class="absolute -right-8 -top-10 h-32 w-32 rounded-full bg-indigo-500/10 blur-3xl"></div>

                <div class="relative flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-indigo-400/20 bg-indigo-500/10 text-indigo-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        @if ($nextTask)
                            <p class="truncate text-base font-bold text-white">
                                {{ $nextTask->task_name }}
                            </p>
                            <p class="mt-1.5 text-sm text-gray-400">
                                Next in your queue
                            </p>
                        @else
                            <p class="text-base font-bold text-white">
                                All caught up
                            </p>
                            <p class="mt-1.5 text-sm text-gray-400">
                                No pending tasks right now
                            </p>
                        @endif
                    </div>
                </div>
            </article>
        </section>

     
        <section class="overflow-hidden rounded-2xl border border-[#262638] bg-[#0d0d19]/90 shadow-2xl shadow-black/10">

            {{-- Queue header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/[0.06] px-5 py-5 sm:px-6">
                <div>
                    <h3 class="text-lg font-bold text-white">
                        Processing Order
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Tasks are arranged by nearest deadline.
                    </p>
                </div>

                <span class="rounded-full border border-white/[0.08] bg-white/[0.04] px-3 py-1.5 text-xs font-semibold text-gray-300">
                    {{ $total }} {{ $total === 1 ? 'task' : 'tasks' }}
                </span>
            </div>

            {{-- Queue body --}}
            <div class="p-4 sm:p-6">
                @if ($total > 0)
                    {{-- Front marker --}}
                    <div class="mb-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-300/80">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                             aria-hidden="true">
                            <path d="M12 19V5" />
                            <path d="m5 12 7-7 7 7" />
                        </svg>
                        Front of queue · Next to process
                    </div>

                    {{-- Task list --}}
                    <div class="flex flex-col gap-3">
                        @foreach ($tasks as $i => $task)
                            @include('partials.task-card', [
                                'task'       => $task,
                                'rank'       => $i + 1,
                                'rankLabel'  => 'Queue #',
                                'badge'      => $i === 0 ? 'Next up' : 'Waiting',
                                'badgeClass' => $i === 0
                                    ? 'bg-blue-500/15 text-blue-300'
                                    : 'bg-orange-500/10 text-orange-300',
                            ])
                        @endforeach
                    </div>

                    {{-- Rear marker --}}
                    <div class="mt-5 flex items-center gap-2 border-t border-white/[0.06] pt-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                             aria-hidden="true">
                            <path d="M12 5v14" />
                            <path d="m19 12-7 7-7-7" />
                        </svg>
                        Rear of queue · Latest to process
                    </div>
                @else
                    {{-- Empty state --}}
                    <div class="flex min-h-[300px] flex-col items-center justify-center px-4 py-12 text-center">
                        <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-2xl border border-blue-400/20 bg-blue-500/[0.08] text-blue-300">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.7"
                                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <rect x="3" y="4" width="18" height="17" rx="2" />
                                <path d="M16 2v4M8 2v4M3 10h18" />
                                <path d="m9 15 2 2 4-4" />
                            </svg>
                        </div>

                        <h4 class="text-lg font-bold text-white">
                            No upcoming tasks
                        </h4>

                        <p class="mt-2 max-w-sm text-sm leading-6 text-gray-400">
                            You're all caught up. Add a task when you have something new to work on.
                        </p>

                        <a href="{{ route('tasks.index') }}"
                           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition hover:brightness-110">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            View Tasks
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection