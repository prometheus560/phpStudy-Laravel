@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $priorityBadge = [
        'High'   => 'bg-rose-500/15 text-rose-400',
        'Medium' => 'bg-yellow-500/15 text-yellow-400',
        'Low'    => 'bg-green-500/15 text-green-400',
    ];
    $priorityBar = [
        'High'   => 'bg-rose-500',
        'Medium' => 'bg-yellow-500',
        'Low'    => 'bg-green-500',
    ];
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Greeting --}}
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white">{{ $greeting }}, {{ $user->name }}</h2>
        <p class="text-gray-400 text-sm mt-1">Here's what's happening in your study planner today.</p>
    </div>

    {{-- Alert banner --}}
    @if ($dueSoon > 0)
        <a href="{{ route('tasks.index') }}"
           class="flex items-center justify-between bg-yellow-500/10 border border-yellow-500/25 text-yellow-300 text-sm rounded-xl px-4 py-3 mb-6 hover:bg-yellow-500/15">
            <span class="flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $dueSoon }} {{ $dueSoon == 1 ? 'task is' : 'tasks are' }} due within the next 3 days
            </span>
            <span>&rsaquo;</span>
        </a>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="w-10 h-10 rounded-lg bg-blue-600/15 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['subjects'] }}</p>
            <p class="text-gray-400 text-sm mt-1">Subjects</p>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="w-10 h-10 rounded-lg bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                @if ($stats['overdue'] > 0)
                    <span class="text-[11px] font-semibold bg-rose-500/15 text-rose-400 px-2 py-0.5 rounded-md">{{ $stats['overdue'] }} overdue</span>
                @endif
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['pending'] }}</p>
            <p class="text-gray-400 text-sm mt-1">Pending Tasks</p>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="w-10 h-10 rounded-lg bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['completed'] }}</p>
            <p class="text-gray-400 text-sm mt-1">Completed Tasks</p>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-start justify-between mb-4">
                <span class="w-10 h-10 rounded-lg bg-rose-500/15 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['high'] }}</p>
            <p class="text-gray-400 text-sm mt-1">High Priority</p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Active workbench --}}
            <div class="bg-[#14141f] border border-blue-500/30 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <h3 class="font-semibold text-blue-300">Active Workbench</h3>
                        <span class="text-xs bg-blue-500/15 text-blue-300 rounded-md px-2 py-0.5">{{ $workbench->count() }} / {{ $stats['pending'] }}</span>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="text-xs text-gray-400 hover:text-white">View All &rarr;</a>
                </div>

                @forelse ($workbench as $task)
                    @php $overdue = $task->deadline->lt(today()); @endphp
                    <div class="flex items-center justify-between gap-4 py-3.5 border-t border-[#23232f]">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $task->task_name }}</p>
                            <span class="inline-block mt-1 text-[10px] font-semibold uppercase tracking-wide bg-white/5 text-gray-400 rounded px-1.5 py-0.5">
                                {{ $task->subject->subject_name }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-semibold rounded-md px-2 py-1 {{ $priorityBadge[$task->priority] ?? 'bg-white/5 text-gray-400' }}">{{ $task->priority }}</span>
                            <span class="text-xs font-mono {{ $overdue ? 'text-rose-400' : 'text-gray-400' }}">{{ $task->deadline->format('M d') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 border-t border-[#23232f]">
                        <p class="text-white font-semibold">You're all caught up</p>
                        <p class="text-gray-400 text-sm mt-1">No pending tasks right now.</p>
                    </div>
                @endforelse
            </div>

            {{-- Recently completed --}}
            <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white">Recently Completed</h3>
                    <a href="{{ route('completed_tasks.index') }}" class="text-xs text-gray-400 hover:text-white">Full List &rarr;</a>
                </div>

                @forelse ($recentCompleted as $task)
                    <div class="flex items-center justify-between gap-4 py-3 border-t border-[#23232f]">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-200 truncate">{{ $task->task_name }}</p>
                            <p class="text-xs text-gray-500">{{ $task->subject->subject_name }}</p>
                        </div>
                        <span class="text-xs font-semibold text-green-400">Completed</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-6 border-t border-[#23232f]">No completed tasks yet.</p>
                @endforelse
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="flex flex-col gap-4">

            {{-- Upcoming --}}
            <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white">Upcoming</h3>
                    <a href="{{ route('upcoming_tasks.index') }}" class="text-xs text-gray-400 hover:text-white">All &rarr;</a>
                </div>

                @forelse ($upcoming as $task)
                    <div class="flex items-stretch gap-3 mb-3 last:mb-0">
                        <span class="w-1 rounded-full {{ $priorityBar[$task->priority] ?? 'bg-gray-500' }}"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $task->task_name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $task->deadline->format('D, M d') }}</p>
                        </div>
                        @if ($task->deadline->isToday())
                            <span class="self-start text-[10px] font-semibold bg-blue-500/15 text-blue-300 rounded-md px-2 py-0.5">Today</span>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-6">Nothing due in the next 7 days.</p>
                @endforelse
            </div>

            {{-- Subject overview --}}
            <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white">Subject Overview</h3>
                    <a href="{{ route('subjects.index') }}" class="text-xs text-gray-400 hover:text-white">Manage &rarr;</a>
                </div>

                @forelse ($subjects as $subject)
                    @php
                        $done = $subject->tasks_count - $subject->pending_tasks_count;
                        $pct = $subject->tasks_count > 0 ? round($done / $subject->tasks_count * 100) : 0;
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-200 truncate">{{ $subject->subject_name }}</span>
                            <span class="text-xs text-gray-500">{{ $subject->pending_tasks_count }} pending &middot; {{ $done }} done</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-6">No subjects yet.</p>
                @endforelse
            </div>

        </div>

    </div>

</div>

@endsection