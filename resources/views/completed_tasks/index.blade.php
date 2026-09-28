@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Completed Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">View the tasks you have already completed.</p>
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

                            <p class="text-gray-400 text-sm">
                                <span class="font-semibold text-gray-300">Deadline:</span>
                                {{ $task->deadline->format('F d, Y') }}
                            </p>
                        </div>

                        <div>
                            <span class="inline-block bg-green-500/15 text-green-400 px-3 py-1.5 rounded-md text-xs font-bold">
                                Completed
                            </span>
                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h3 class="font-semibold text-white text-lg">No completed tasks</h3>
                <p class="text-gray-400 text-sm mt-1">You don't have any completed tasks yet.</p>

                <div class="mt-4">
                    <a href="{{ route('tasks.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        View Tasks
                    </a>
                </div>
            </div>

        @endif

    </div>

</div>

@endsection