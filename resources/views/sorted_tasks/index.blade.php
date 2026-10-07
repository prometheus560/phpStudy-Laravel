@extends('layouts.app')

@section('title', 'Sorted Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Sorted Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">Your tasks sorted by deadline, earliest first, using Bubble Sort.</p>
    </div>

    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm px-5">

        @if (count($tasks) > 0)

            <div class="flex flex-col divide-y divide-[#23232f]">

                @foreach ($tasks as $task)

                    <div class="flex flex-wrap sm:flex-nowrap items-start sm:items-center justify-between gap-4 py-5">

                        <div class="flex-1 min-w-[240px]">
                            <h3 class="text-blue-400 font-semibold text-lg mb-2">
                                {{ $task->task_name }}
                            </h3>

                            <p class="text-gray-400 text-sm mb-1.5">
                                <span class="font-semibold text-gray-300">Subject:</span>
                                {{ $task->subject->subject_name }}
                            </p>

                            <p class="text-blue-400 font-bold text-sm">
                                <span class="font-semibold">Deadline:</span>
                                {{ $task->deadline->format('F d, Y') }}
                            </p>
                        </div>

                        <div>
                            @if ($task->priority === 'High')
                                <span class="inline-block bg-rose-500/15 text-rose-400 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap">High Priority</span>
                            @elseif ($task->priority === 'Medium')
                                <span class="inline-block bg-yellow-500/15 text-yellow-400 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap">Medium Priority</span>
                            @else
                                <span class="inline-block bg-green-500/15 text-green-400 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap">Low Priority</span>
                            @endif
                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h3 class="font-semibold text-white text-lg">No tasks available</h3>
                <p class="text-gray-400 text-sm mt-1">You don't have any tasks yet.</p>

                <div class="mt-4">
                    <a href="{{ route('tasks.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Add Task
                    </a>
                </div>
            </div>

        @endif

    </div>

    {{-- How the sort worked --}}
    @if (count($passes) > 0)
        <details class="mt-6 bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <summary class="cursor-pointer text-sm font-semibold text-blue-400">
                See how Bubble Sort did it
            </summary>

            <p class="text-gray-400 text-sm mt-3">
                Bubble Sort compares two neighbors at a time and swaps them if the first one is later.
                It repeats until a full pass needs no swaps.
                Here it made {{ $comparisons }} {{ $comparisons == 1 ? 'comparison' : 'comparisons' }}
                and {{ $swaps }} {{ $swaps == 1 ? 'swap' : 'swaps' }}.
            </p>

            <div class="mt-4 flex flex-col gap-4">
                @foreach ($passes as $pass)
                    <div>
                        <p class="text-sm font-semibold text-gray-200">
                            Pass {{ $pass['number'] }}
                            <span class="font-normal text-gray-400">
                                &middot;
                                @if ($pass['swaps'] === 0)
                                    no swaps, so it's sorted
                                @else
                                    {{ $pass['swaps'] }} {{ $pass['swaps'] == 1 ? 'swap' : 'swaps' }}
                                @endif
                            </span>
                        </p>
                        <ol class="mt-1.5 text-sm text-gray-400 list-decimal list-inside">
                            @foreach ($pass['order'] as $name)
                                <li class="truncate">{{ $name }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </details>
    @endif

</div>

@endsection