@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')

@php
    $pendingCount   = $tasks->where('status', '!=', 'Completed')->count();
    $completedCount = $tasks->where('status', 'Completed')->count();
    $overdueCount   = $tasks->filter(fn ($t) => $t->is_overdue)->count();

    $accent = [
        'High'   => 'border-l-rose-500',
        'Medium' => 'border-l-yellow-500',
        'Low'    => 'border-l-green-500',
    ];

    $icTask = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>';
    $icBook = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
    $icCal  = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    $icFlag = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="page-title">My Tasks</h2>
            <p class="page-sub">Create, manage, and track your study tasks.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Delete All --}}
            <form
                method="POST"
                action="{{ route('tasks.destroy_all') }}"
                onsubmit="return confirm('Are you sure you want to delete all tasks? This action cannot be undone.');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    @disabled($tasks->isEmpty())
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold border border-red-500/40 text-red-300 bg-red-500/10 hover:bg-red-500/20 disabled:opacity-40 disabled:cursor-not-allowed transition"
                    title="{{ $tasks->isEmpty() ? 'No tasks to delete.' : 'Delete all your tasks' }}"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                    Delete All
                </button>
            </form>

            @if ($subjects->count() > 0)
                <button type="button" data-open-add
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Task
                </button>
            @else
                <a href="{{ route('subjects.index') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">
                    Add Subject first
                </a>
            @endif
        </div>
    </div>

    @if ($tasks->isEmpty())
        <p class="text-sm text-gray-400 mb-4">No tasks to delete.</p>
    @endif

    @if (session('success'))
        <div class="alert alert-ok" role="status">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert" role="status">{{ session('info') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err" role="alert">{{ $errors->first() }}</div>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $pendingCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Pending</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $completedCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Completed</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $overdueCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Overdue</p>
            </div>
        </div>

    </div>

    {{-- Task List --}}
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-bold text-white">All Tasks</h3>
            <span class="chip chip-blue">{{ $tasks->count() }} {{ $tasks->count() == 1 ? 'task' : 'tasks' }}</span>
        </div>
        <p class="text-sm mt-1" style="color: var(--muted)">Everything in your planner, soonest deadline first.</p>

        @if ($tasks->count() > 0)
            <div class="mt-5">
                @include('partials.list-search', [
                    'placeholder' => 'Type a letter to find a task...',
                    'filterAll'   => 'All subjects',
                    'filters'     => $subjects->pluck('subject_name', 'id')->all(),
                    'target'      => '#task-list',
                    'emptyText'   => 'No task matches your search.',
                ])
            </div>
        @endif
    </div>

    @if ($tasks->count() > 0)

        <div class="flex items-center mb-3 px-1">
            <label class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-200 cursor-pointer select-none">
                <input type="checkbox" id="select-all" class="w-4 h-4 accent-blue-500 cursor-pointer">
                Select all
            </label>
        </div>

        <div id