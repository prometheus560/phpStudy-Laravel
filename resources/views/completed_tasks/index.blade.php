@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">Completed Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">
            The tasks you have finished, most recent first, using a Stack (last in, first out).
        </p>
    </div>

    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-white">Pop order</h3>
            <span class="text-xs font-semibold text-gray-400">{{ count($tasks) }} {{ count($tasks) == 1 ? 'task' : 'tasks' }}</span>
        </div>

        @if (count($tasks) > 0)
            <div class="flex flex-col gap-3">
                @foreach ($tasks as $i => $task)
                    @include('partials.task-card', [
                        'task' => $task, 'rank' => $i + 1, 'rankLabel' => 'Popped #',
                        'badge' => $i === 0 ? 'Most recent' : 'Completed',
                        'badgeClass' => 'bg-green-500/15 text-green-400',
                    ])
                @endforeach
            </div>
        @else
            <div class="text-center py-14">
                <h4 class="font-semibold text-white text-lg">No completed tasks</h4>
                <p class="text-gray-400 text-sm mt-1">You don't have any completed tasks yet.</p>
                <a href="{{ route('tasks.index') }}" class="inline-block mt-4 bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500">View Tasks</a>
            </div>
        @endif
    </div>
</div>

@endsection