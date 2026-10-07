@php
    $bars   = ['High' => 'bg-rose-500', 'Medium' => 'bg-yellow-500', 'Low' => 'bg-green-500'];
    $badges = [
        'High'   => 'bg-rose-500/15 text-rose-400',
        'Medium' => 'bg-yellow-500/15 text-yellow-400',
        'Low'    => 'bg-green-500/15 text-green-400',
    ];

    $isDone = $task->status === 'Completed';
    $days   = (int) today()->diffInDays($task->deadline->copy()->startOfDay(), false);
    $tone   = $isDone ? 'text-green-400' : ($task->is_overdue ? 'text-rose-400' : ($days <= 2 ? 'text-amber-400' : 'text-gray-400'));
@endphp

<div class="flex overflow-hidden rounded-xl border border-[#23232f] bg-[#1b1b28]">
    <span class="w-1 shrink-0 {{ $bars[$task->priority] ?? 'bg-gray-500' }}"></span>

    <div class="flex-1 p-4 flex flex-wrap items-center justify-between gap-4">

        <div class="flex items-center gap-4 flex-1 min-w-[220px]">
            <span title="{{ $rankLabel }} {{ $rank }}"
                  class="w-9 h-9 shrink-0 grid place-items-center rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-300 text-sm font-bold">{{ $rank }}</span>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h4 class="font-semibold text-white">{{ $task->task_name }}</h4>
                    <span class="text-[11px] font-bold rounded-md px-2 py-0.5 {{ $badges[$task->priority] ?? 'bg-white/5 text-gray-400' }}">{{ $task->priority }}</span>
                </div>

                <p class="text-sm text-gray-400 mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span class="text-[11px] font-semibold uppercase tracking-wide bg-white/5 text-gray-300 rounded px-2 py-0.5">{{ $task->subject->subject_name }}</span>
                    <span>{{ $task->deadline->format('M d, Y') }}</span>
                    @unless ($isDone)
                        <span class="font-semibold {{ $tone }}">{{ $task->due_label }}</span>
                    @endunless
                </p>
            </div>
        </div>

        <span class="text-[11px] font-bold rounded-md px-3 py-1.5 {{ $badgeClass }}">{{ $badge }}</span>
    </div>
</div>