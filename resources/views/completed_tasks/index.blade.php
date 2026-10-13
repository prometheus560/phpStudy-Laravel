@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')

@php
    $list      = collect($tasks);
    $total     = $list->count();
    $latest    = $list->first();
    $subjCount = $list->map(fn ($t) => optional($t->subject)->subject_name)->filter()->unique()->count();
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-3xl font-bold text-white tracking-tight">Completed Tasks</h2>
                <span class="chip chip-blue">Stack &middot; LIFO</span>
            </div>
            <p class="text-gray-400 text-sm mt-1">The tasks you have finished, most recent first.</p>
        </div>

        <a href="{{ route('tasks.index') }}" class="btn-ghost">&larr; Back to Tasks</a>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        <div class="stat" style="--glow:rgba(34,197,94,.18);--c:#4ade80;--bg:rgba(34,197,94,.12);--bd:rgba(34,197,94,.25)">
            <div class="flex items-center gap-4 relative">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                <div>
                    <p class="text-2xl font-bold text-white leading-none">{{ $total }}</p>
                    <p class="text-gray-400 text-sm mt-1">{{ $total == 1 ? 'Task completed' : 'Tasks completed' }}</p>
                </div>
            </div>
        </div>

        <div class="stat" style="--glow:rgba(59,130,246,.22);--c:#60a5fa;--bg:rgba(59,130,246,.12);--bd:rgba(59,130,246,.25)">
            <div class="flex items-center gap-4 relative">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                <div>
                    <p class="text-2xl font-bold text-white leading-none">{{ $subjCount }}</p>
                    <p class="text-gray-400 text-sm mt-1">{{ $subjCount == 1 ? 'Subject covered' : 'Subjects covered' }}</p>
                </div>
            </div>
        </div>

        <div class="stat" style="--glow:rgba(139,92,246,.22);--c:#a78bfa;--bg:rgba(139,92,246,.12);--bd:rgba(139,92,246,.25)">
            <div class="flex items-center gap-4 relative min-w-0">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></span>
                <div class="min-w-0">
                    @if ($latest)
                        <p class="text-base font-bold text-white leading-tight truncate">{{ $latest->task_name }}</p>
                        <p class="text-gray-400 text-sm mt-1">Top of the stack</p>
                    @else
                        <p class="text-base font-bold text-white leading-tight">Empty stack</p>
                        <p class="text-gray-400 text-sm mt-1">Nothing finished yet</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Pop order --}}
    <div class="flex items-end justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-bold text-white">Pop order</h3>
            <p class="text-gray-500 text-sm mt-1">The newest finished task leaves the stack first.</p>
        </div>
        <span class="chip chip-muted">{{ $total }} {{ $total == 1 ? 'task' : 'tasks' }}</span>
    </div>

    @if ($total > 0)

        <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-300/80 mb-3">&darr; Top of stack</p>

        <div class="flex flex-col gap-3">
            @foreach ($tasks as $i => $task)
                @include('partials.task-card', [
                    'task' => $task, 'rank' => $i + 1, 'rankLabel' => 'Popped #',
                    'badge' => $i === 0 ? 'Most recent' : 'Completed',
                    'badgeClass' => 'bg-green-500/15 text-green-400',
                ])
            @endforeach
        </div>

        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-600 mt-4">&uarr; Bottom of stack</p>

    @else

        <div class="glass text-center py-16 px-6">
            <span class="w-14 h-14 mx-auto rounded-2xl bg-green-500/12 border border-green-500/25 text-green-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <h4 class="font-semibold text-white text-lg">No completed tasks yet</h4>
            <p class="text-gray-400 text-sm mt-1 mb-5">Finish a task and it will be pushed onto this stack.</p>
            <a href="{{ route('tasks.index') }}" class="btn-primary">View Tasks</a>
        </div>

    @endif

</div>

@endsection