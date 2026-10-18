@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')

@php
    $list = collect($tasks);
    $total = $list->count();

    $subjCount = $list
        ->map(fn ($task) => optional($task->subject)->subject_name)
        ->filter()
        ->unique()
        ->count();
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

    {{-- Header --}}
    <header class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <span class="flex items-center justify-center w-10 h-10 rounded-xl
                         bg-green-500/10 text-green-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </span>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Completed Tasks
            </h1>
        </div>

        <p class="text-sm text-gray-400">
            Your finished tasks, newest first.
        </p>
    </header>

    {{-- Statistics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

        <article class="rounded-2xl border border-white/[0.08] bg-white/[0.03] p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-400">Completed</p>
                    <p class="mt-2 text-3xl font-bold text-white">
                        {{ $total }}
                    </p>
                </div>

                <span class="flex items-center justify-center w-11 h-11 rounded-xl
                             bg-green-500/10 text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="m5 12 4 4L19 6"/>
                    </svg>
                </span>
            </div>
        </article>

        <article class="rounded-2xl border border-white/[0.08] bg-white/[0.03] p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-400">Subjects</p>
                    <p class="mt-2 text-3xl font-bold text-white">
                        {{ $subjCount }}
                    </p>
                </div>

                <span class="flex items-center justify-center w-11 h-11 rounded-xl
                             bg-blue-500/10 text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                         stroke-width="1.8" stroke-linecap="round"
                         stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </span>
            </div>
        </article>

    </section>

    @if ($total > 0)

        {{-- Completed Tasks --}}
        <div class="flex flex-col gap-3">

            @foreach ($list as $task)
                @php
                    $priority = strtolower($task->priority ?? '');

                    $priorityStyles = [
                        'high' => 'border-red-400/20 bg-red-500/10 text-red-300',
                        'medium' => 'border-amber-400/20 bg-amber-500/10 text-amber-300',
                        'low' => 'border-blue-400/20 bg-blue-500/10 text-blue-300',
                    ];

                    $priorityClass = $priorityStyles[$priority]
                        ?? 'border-white/10 bg-white/[0.04] text-gray-300';
                @endphp

                <article class="rounded-2xl border border-white/[0.08] bg-white/[0.025]
                                transition hover:border-white/[0.14] hover:bg-white/[0.04]">

                    <div class="flex items-start gap-4 p-4 sm:p-5">

                        {{-- Completed Icon --}}
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center
                                     rounded-xl bg-green-500/10 text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="m5 12 4 4L19 6"/>
                            </svg>
                        </span>

                        {{-- Task Details --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-start
                                        sm:justify-between gap-3">

                                <div class="min-w-0">
                                    <h2 class="text-sm sm:text-base font-semibold
                                               leading-6 text-white break-words">
                                        {{ $task->task_name }}
                                    </h2>

                                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-2">

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

                                        @if ($task->updated_at)
                                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                                <svg class="w-3.5 h-3.5" fill="none"
                                                     stroke="currentColor" stroke-width="1.7"
                                                     stroke-linecap="round" stroke-linejoin="round"
                                                     viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10"/>
                                                    <polyline points="12 6 12 12 16 14"/>
                                                </svg>

                                                {{ $task->updated_at->format('M d, Y') }}
                                            </span>
                                        @endif

                                        @if ($priority !== '')
                                            <span class="inline-flex items-center rounded-full border
                                                         px-2.5 py-1 text-xs font-medium
                                                         {{ $priorityClass }}">
                                                {{ ucfirst($priority) }} Priority
                                            </span>
                                        @endif

                                    </div>
                                </div>

                                {{-- Status --}}
                                <span class="inline-flex shrink-0 items-center gap-1.5
                                             self-start rounded-full border border-green-500/20
                                             bg-green-500/10 px-2.5 py-1 text-xs
                                             font-medium text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-400"></span>
                                    Completed
                                </span>

                            </div>
                        </div>

                    </div>
                </article>
            @endforeach

        </div>

    @else

        {{-- Empty State --}}
        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.025]
                    px-6 py-14 text-center">

            <span class="mx-auto flex h-14 w-14 items-center justify-center
                         rounded-2xl bg-green-500/10 text-green-400">
                <svg class="w-7 h-7" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round"
                     stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </span>

            <h2 class="mt-5 text-lg font-semibold text-white">
                No completed tasks yet
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                Tasks you complete will appear here.
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