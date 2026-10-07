@extends('layouts.app')

@section('title', 'Upcoming Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">Upcoming Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">Your pending tasks, handled from the nearest deadline to the latest.</p>
    </div>

    <x-ds-panel 
                
                complexity="enqueue O(1) · dequeue O(n) (PHP array_shift re-indexes the array)"
                :trace="$trace">

        @if (count($waiting) > 0)
            <div class="flex items-center gap-2 min-w-max">
                <span class="text-[11px] font-bold uppercase tracking-wider text-green-400">Front</span>

                @foreach (array_slice($waiting, 0, 8) as $i => $t)
                    <span class="shrink-0 rounded-lg border px-3 py-1.5 text-xs font-medium {{ $i === 0 ? 'border-blue-500/50 bg-blue-500/15 text-blue-200' : 'border-[#2a2a38] bg-[#12121c] text-gray-300' }}">
                        {{ \Illuminate\Support\Str::limit($t->task_name, 16) }}
                    </span>
                    @if (! $loop->last) <span class="text-gray-600">&rarr;</span> @endif
                @endforeach

                @if (count($waiting) > 8)
                    <span class="text-xs text-gray-500">+{{ count($waiting) - 8 }} more</span>
                @endif

                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Rear</span>
            </div>
        @else
            <p class="text-sm text-gray-500">The queue is empty.</p>
        @endif
    </x-ds-panel>

    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-white">Processing order</h3>
            <span class="text-xs font-semibold text-gray-400">{{ count($tasks) }} {{ count($tasks) == 1 ? 'task' : 'tasks' }}</span>
        </div>

        @if (count($tasks) > 0)
            <div class="flex flex-col gap-3">
                @foreach ($tasks as $i => $task)
                    @include('partials.task-card', [
                        'task' => $task, 'rank' => $i + 1, 'rankLabel' => 'Dequeued #',
                        'badge' => $i === 0 ? 'Next up' : 'Waiting',
                        'badgeClass' => $i === 0 ? 'bg-blue-500/15 text-blue-300' : 'bg-orange-500/10 text-orange-300',
                    ])
                @endforeach
            </div>
        @else
            <div class="text-center py-14">
                <h4 class="font-semibold text-white text-lg">No upcoming tasks</h4>
                <p class="text-gray-400 text-sm mt-1">You're all caught up. Nothing pending right now.</p>
                <a href="{{ route('tasks.index') }}" class="inline-block mt-4 bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500">View Tasks</a>
            </div>
        @endif
    </div>
</div>

@endsection