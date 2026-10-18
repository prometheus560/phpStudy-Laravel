@extends('layouts.app')

@section('title', 'Upcoming Tasks')

@section('content')

@php
    $taskList = collect($tasks);
    $total = $taskList->count();
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

    {{-- Header --}}
    <header class="flex flex-wrap items-center justify-between gap-4 mb-7">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Upcoming Tasks
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                {{ $total }} {{ $total === 1 ? 'pending task' : 'pending' }}
            </p>
        </div>

        <a href="{{ route('tasks.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-white/10
                  bg-white/[0.04] px-4 py-2.5 text-sm font-medium text-gray-300
                  transition hover:bg-white/[0.08] hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round"
                 stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="m15 18-6-6 6-6"/>
                <path d="M20 12H9"/>
            </svg>
            All Tasks
        </a>
    </header>

    @if ($total > 0)

        {{-- Task List --}}
        <div class="flex flex-col gap-3">

            @foreach ($taskList as $task)
                @php
                    $priority = strtolower($task->priority ?? 'medium');

                    $priorityStyles = [
                        'high' => 'bg-red-500/10 text-red-300 border-red-400/20',
                        'medium' => 'bg-amber-500/10 text-amber-300 border-amber-400/20',
                        'low' => 'bg-blue-500/10 text-blue-300 border-blue-400/20',
                    ];

                    $priorityClass = $priorityStyles[$priority]
                        ?? 'bg-white/[0.05] text-gray-300 border-white/10';

                    $dueDate = $task->deadline
                        ? \Illuminate\Support\Carbon::parse($task->deadline)->startOfDay()
                        : null;

                    $today = now()->startOfDay();

                    if (!$dueDate) {
                        $dueLabel = 'No due date';
                    } elseif ($dueDate->lt($today)) {
                        $dueLabel = 'Overdue';
                    } elseif ($dueDate->equalTo($today)) {
                        $dueLabel = 'Due today';
                    } elseif ($dueDate->equalTo($today->copy()->addDay())) {
                        $dueLabel = 'Due tomorrow';
                    } else {
                        $daysLeft = (int) $today->diffInDays($dueDate);
                        $dueLabel = $daysLeft . ' days left';
                    }
                @endphp

                <article
                    class="group rounded-2xl border border-white/[0.08] bg-white/[0.025]
                           transition hover:border-white/[0.14] hover:bg-white/[0.04]"
                >
                    <div class="flex items-start gap-3 sm:gap-4 p-4 sm:p-5">

                        {{-- Task Icon --}}
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-xl border border-blue-400/15
                                    bg-blue-500/[0.08] text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>

                        {{-- Task Information --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start
                                        sm:justify-between gap-3">

                                <div class="min-w-0">
                                    <h2 class="text-sm sm:text-base font-semibold
                                               leading-6 text-white break-words">
                                        {{ $task->task_name }}
                                    </h2>

                                    <div class="flex flex-wrap items-center gap-2 mt-2">

                                        @if ($task->subject)
                                            <span class="inline-flex items-center gap-1.5
                                                         text-xs text-gray-400">
                                                <svg class="w-3.5 h-3.5" fill="none"
                                                     stroke="currentColor" stroke-width="1.7"
                                                     stroke-linecap="round" stroke-linejoin="round"
                                                     viewBox="0 0 24 24">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                </svg>

                                                {{ $task->subject->subject_name }}
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center rounded-full
                                                     border px-2.5 py-1 text-xs font-medium
                                                     {{ $priorityClass }}">
                                            {{ ucfirst($priority) }} Priority
                                        </span>

                                    </div>
                                </div>

                                {{-- Due Date --}}
                                <div class="sm:text-right shrink-0">
                                    <p class="text-xs text-gray-500">Due date</p>

                                    <p class="text-sm font-medium text-gray-200 mt-1">
                                        @if ($dueDate)
                                            {{ $dueDate->format('M d, Y') }}
                                        @else
                                            Not set
                                        @endif
                                    </p>

                                    <p class="text-xs mt-1
                                        {{ $dueLabel === 'Overdue'
                                            ? 'text-red-400'
                                            : ($dueLabel === 'Due today'
                                                ? 'text-amber-300'
                                                : 'text-gray-400') }}">
                                        {{ $dueLabel }}
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>
                </article>
            @endforeach

        </div>

    @else

        {{-- Empty State --}}
        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.025]
                    px-6 py-16 text-center">

            <div class="mx-auto flex h-14 w-14 items-center justify-center
                        rounded-2xl border border-blue-400/15
                        bg-blue-500/[0.08] text-blue-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor"
                     stroke-width="1.7" stroke-linecap="round"
                     stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                    <path d="m9 15 2 2 4-4"/>
                </svg>
            </div>

            <h2 class="mt-5 text-lg font-semibold text-white">
                No upcoming tasks
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                You have no pending tasks right now.
            </p>

            <a href="{{ route('tasks.index') }}"
               class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl
                      bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white
                      transition hover:bg-blue-500">
                View Tasks

                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </a>

        </div>

    @endif

</div>

@endsection