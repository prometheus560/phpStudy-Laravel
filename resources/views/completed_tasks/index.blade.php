@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">Completed Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">The tasks you have finished, most recent first.</p>
    </div>

  
        @if (count($pushed) > 0)
            @php $top = array_slice(array_reverse($pushed), 0, 6); @endphp
            <div class="flex flex-col items-start gap-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-green-400">Top &darr;</span>

                @foreach ($top as $i => $t)
                    <span class="w-full max-w-sm rounded-lg border px-3 py-1.5 text-xs font-medium {{ $i === 0 ? 'border-green-500/50 bg-green-500/15 text-green-200' : 'border-[#2a2a38] bg-[#12121c] text-gray-300' }}">
                        {{ \Illuminate\Support\Str::limit($t->task_name, 36) }}
                    </span>
                @endforeach

                @if (count($pushed) > 6)
                    <span class="text-xs text-gray-500">+{{ count($pushed) - 6 }} more below</span>
                @endif

                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Bottom</span>
            </div>
        @else
            <p class="text-sm text-gray-500">The stack is empty.</p>
  
    </x-ds-panel>

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