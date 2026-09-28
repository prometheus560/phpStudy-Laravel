@extends('layouts.app')

@section('title', 'Priority Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Priority Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">Your pending tasks organized from highest to lowest priority.</p>
    </div>

    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">

        @if ($tasks->count() > 0)

            <div class="flex flex-col gap-4">

                @foreach ($tasks as $task)

                    <div class="bg-[#1b1b28] border border-[#23232f] rounded-xl p-5 flex flex-wrap items-center justify-between gap-4">

                        <div class="flex-1 min-w-[240px]">
                            <h3 class="text-blue-400 font-semibold text-lg mb-2">
                                {{ $task->task_name }}
                            </h3>

                            <p class="text-gray-400 text-sm mb-1">
                                <span class="font-semibold text-gray-300">Subject:</span>
                                {{ $task->subject->subject_name }}
                            </p>

                            <p class="text-gray-400 text-sm mb-2">
                                <span class="font-semibold text-gray-300">Deadline:</span>
                                {{ $task->deadline->format('F d, Y') }}
                            </p>

                            <p class="text-gray-400 text-sm">
                                <span class="font-semibold text-gray-300">Priority:</span>
                                @if ($task->priority === 'High')
                                    <span class="inline-block bg-rose-500/15 text-rose-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">High</span>
                                @elseif ($task->priority === 'Medium')
                                    <span class="inline-block bg-yellow-500/15 text-yellow-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">Medium</span>
                                @else
                                    <span class="inline-block bg-green-500/15 text-green-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">Low</span>
                                @endif
                            </p>
                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h3 class="font-semibold text-white text-lg">No pending tasks</h3>
                <p class="text-gray-400 text-sm mt-1">You don't have any pending tasks.</p>

                <div class="mt-4">
                    <a href="{{ route('tasks.index') }}"
                       class="inline-block bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">
                        View Tasks
                    </a>
                </div>
            </div>

        @endif

    </div>

</div>

@endsection