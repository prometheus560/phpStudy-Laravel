@extends('layouts.app')

@section('title', 'Upcoming Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">
            Upcoming Tasks
        </h2>

        <p class="text-gray-400 text-sm mt-1">
            Your pending tasks, handled from the nearest deadline to the latest.
        </p>
    </div>


    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="flex items-center justify-between mb-5">

            <h3 class="text-lg font-bold text-white">
                Processing order
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
                        'badge' => $i === 0 ? 'Next up' : 'Waiting',
                        'badgeClass' => $i === 0
                            ? 'bg-blue-500/15 text-blue-300'
                            : 'bg-orange-500/10 text-orange-300',
                    ])

                @endforeach

            </div>

        @else

            <div class="text-center py-14">

                <h4 class="font-semibold text-white text-lg">
                    No upcoming tasks
                </h4>

                <p class="text-gray-400 text-sm mt-1">
                    You're all caught up. Nothing pending right now.
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