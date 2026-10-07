@extends('layouts.app')

@section('title', 'Priority Tasks')

@section('content')

@php
    $lane = [
        'High'   => ['border-rose-500/30 bg-rose-500/5', 'text-rose-400', 'border-rose-500/40 bg-rose-500/10 text-rose-200'],
        'Medium' => ['border-yellow-500/30 bg-yellow-500/5', 'text-yellow-400', 'border-yellow-500/40 bg-yellow-500/10 text-yellow-200'],
        'Low'    => ['border-green-500/30 bg-green-500/5', 'text-green-400', 'border-green-500/40 bg-green-500/10 text-green-200'],
    ];
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">
            Priority Tasks
        </h2>

        <p class="text-gray-400 text-sm mt-1">
            Your pending tasks, from highest to lowest priority.
        </p>
    </div>


    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="flex items-center justify-between mb-5">

            <h3 class="text-lg font-bold text-white">
                Task in order
            </h3>

            <span class="text-xs font-semibold text-gray-400">
                {{ count($tasks) }}
                {{ count($tasks) == 1 ? 'task' : 'tasks' }}
            </span>

        </div>


        @if (count($tasks) > 0)

            <div class="flex flex-col gap-3">

                @foreach ($tasks as $i => $task)

                    @include('partials.task-card', [
                        'task' => $task,
                        'rank' => $i + 1,
                        'rankLabel' => 'Dequeued #',
                        'badge' => $i === 0
                            ? 'Do this first'
                            : $task->priority . ' priority',
                        'badgeClass' => $i === 0
                            ? 'bg-blue-500/15 text-blue-300'
                            : 'bg-white/5 text-gray-400',
                    ])

                @endforeach

            </div>

        @else

            <div class="text-center py-14">

                <h4 class="font-semibold text-white text-lg">
                    No pending tasks
                </h4>

                <p class="text-gray-400 text-sm mt-1">
                    You don't have any pending tasks.
                </p>

                <a
                    href="{{ route('tasks.index') }}"
                    class="inline-block mt-4 bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500"
                >
                    View Tasks
                </a>

            </div>

        @endif

    </div>

</div>

@endsection