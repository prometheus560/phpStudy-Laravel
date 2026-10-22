@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $priorityChip = [
        'High'   => 'chip-high',
        'Medium' => 'chip-med',
        'Low'    => 'chip-low',
    ];

    // Countdown text for a deadline (e.g. "Overdue by 2d", "Due today", "5 days left")
    $dueLabel = function ($deadline) {
        $days = (int) today()->diffInDays($deadline->copy()->startOfDay(), false);
        if ($days < 0)   return 'Overdue by ' . abs($days) . 'd';
        if ($days === 0) return 'Due today';
        if ($days === 1) return 'Due tomorrow';
        return $days . ' days left';
    };

    // Overall progress
    $overallTotal = $stats['pending'] + $stats['completed'];
    $overallPct   = $overallTotal > 0 ? (int) round($stats['completed'] / $overallTotal * 100) : 0;

    // Greeting line
    $todayCount = $sessions->filter(fn ($s) => $s->study_date->isToday())->count();
    $parts = [];
    if ($dueThisWeek > 0) { $parts[] = $dueThisWeek . ($dueThisWeek == 1 ? ' task' : ' tasks') . ' due this week'; }
    if ($todayCount > 0)  { $parts[] = $todayCount . ($todayCount == 1 ? ' study session' : ' study sessions') . ' today'; }
    $summary = count($parts) ? 'You have ' . implode(' and ', $parts) . '.' : 'Nothing urgent right now. A good day to get ahead.';

    // Subjects with the most pending work first, top 5
    $topSubjects = $subjects->sortByDesc('pending_tasks_count')->take(5);
@endphp

<div class="max-w-6xl mx-auto px-4 py-5 sm:px-6 sm:py-8">

    {{-- Greeting --}}
    <div class="flex flex-wrap items-center justify-between gap-4 sm:gap-5 mb-5 sm:mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">{{ $greeting }}, <span class="grad-text">{{ $user->name }}</span></h2>
            <p class="text-gray-400 text-sm mt-1">{{ $summary }}</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto rounded-2xl border border-white/10 bg-white/[.03] px-4 py-3 sm:rounded-none sm:border-0 sm:bg-transparent sm:p-0">
            <div class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-full grid place-items-center"
                 style="background: conic-gradient(#6366f1 {{ $overallPct }}%, rgba(255,255,255,.08) 0)">
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full grid place-items-center text-[10px] sm:text-xs font-bold text-white" style="background:#0c0b1a">{{ $overallPct }}%</div>
            </div>
            <div>
                <p class="text-sm font-semibold text-white leading-tight">Overall progress</p>
                <p class="text-xs text-gray-500">{{ $stats['completed'] }} of {{ $overallTotal }} tasks done</p>
            </div>
        </div>
    </div>

    {{-- Success message after clicking Done --}}
    @if (session('success'))
        <div class="alert alert-ok" role="status">{{ session('success') }}</div>
    @endif

    {{-- Alert banner --}}
    @if ($dueSoon > 0)
        <a href="{{ route('tasks.index') }}"
           class="flex items-center justify-between bg-yellow-500/10 border border-yellow-500/25 text-yellow-300 text-[13px] sm:text-sm rounded-xl px-4 py-3 mb-4 sm:mb-6 hover:bg-yellow-500/15">
            <span class="flex items-center gap-3">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>
                    {{ $dueSoon }} {{ $dueSoon == 1 ? 'task is' : 'tasks are' }} due soon:
                    <strong class="font-semibold">
                        @foreach (collect($dueSoonTasks ?? [])->take(2) as $t)
                            {{ \Illuminate\Support\Str::limit($t->task_name, 40) }} ({{ strtolower($dueLabel($t->deadline)) }}){{ !$loop->last ? ',' : '' }}
                        @endforeach
                        @if ($dueSoon > 2) and {{ $dueSoon - 2 }} more @endif
                    </strong>
                </span>
            </span>
            <span>&rsaquo;</span>
        </a>
    @endif

    {{-- Stat cards (each one opens its page) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">

        <a href="{{ route('subjects.index') }}" class="stat block"
           style="--glow:rgba(59,130,246,.22);--c:#60a5fa;--bg:rgba(59,130,246,.12);--bd:rgba(59,130,246,.25)">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-white relative">{{ $stats['subjects'] }}</p>
            <p class="text-gray-400 text-[13px] sm:text-sm mt-1 relative">Subjects</p>
        </a>

        <a href="{{ route('tasks.index') }}" class="stat block"
           style="--glow:rgba(249,115,22,.20);--c:#fb923c;--bg:rgba(249,115,22,.12);--bd:rgba(249,115,22,.25)">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                @if ($stats['overdue'] > 0)
                    <span class="relative text-[11px] font-semibold bg-rose-500/15 text-rose-400 px-2 py-0.5 rounded-md">{{ $stats['overdue'] }} overdue</span>
                @endif
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-white relative">{{ $stats['pending'] }}</p>
            <p class="text-gray-400 text-[13px] sm:text-sm mt-1 relative">Pending Tasks</p>
        </a>

        <a href="{{ route('completed_tasks.index') }}" class="stat block"
           style="--glow:rgba(34,197,94,.18);--c:#4ade80;--bg:rgba(34,197,94,.12);--bd:rgba(34,197,94,.25)">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-white relative">{{ $stats['completed'] }}</p>
            <p class="text-gray-400 text-[13px] sm:text-sm mt-1 relative">Completed Tasks</p>
        </a>

        <a href="{{ route('priority_tasks.index') }}" class="stat block"
           style="--glow:rgba(244,63,94,.18);--c:#fb7185;--bg:rgba(244,63,94,.12);--bd:rgba(244,63,94,.25)">
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <span class="stat-icon"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-white relative">{{ $stats['high'] }}</p>
            <p class="text-gray-400 text-[13px] sm:text-sm mt-1 relative">High Priority</p>
        </a>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 flex flex-col gap-3 sm:gap-4">

            {{-- Active workbench --}}
            <div class="glass glass-accent p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <span class="pulse-dot"></span>
                        <h3 class="font-semibold text-blue-300">Active Workbench</h3>
                        <span class="chip chip-blue">{{ $workbench->count() }} / {{ $stats['pending'] }}</span>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="text-xs text-gray-400 hover:text-white">View All &rarr;</a>
                </div>

                @forelse ($workbench as $task)
                    @php
                        $days  = (int) today()->diffInDays($task->deadline->copy()->startOfDay(), false);
                        $dueCl = $days < 0 ? 'text-rose-400' : ($days === 0 ? 'text-amber-300' : 'text-gray-400');
                    @endphp
                    <div class="row-item flex flex-wrap items-center justify-between gap-3 py-3.5 px-2 -mx-2 rounded-lg">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $task->task_name }}</p>
                            <span class="inline-block mt-1 chip chip-muted" style="font-size:10px;padding:2px 8px">{{ $task->subject->subject_name }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 w-full sm:w-auto sm:shrink-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="chip {{ $priorityChip[$task->priority] ?? 'chip-muted' }}">{{ $task->priority }}</span>
                                <span class="text-xs {{ $dueCl }}">{{ $task->deadline->format('M d') }} &middot; {{ $dueLabel($task->deadline) }}</span>
                            </div>
                            <form method="POST" action="{{ route('tasks.complete', $task) }}" data-once>
                                @csrf
                                @method('PATCH')
                                <button type="submit" title="Mark as done" class="btn-sm btn-ok">&#10003; Done</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 border-t border-white/10">
                        <span class="w-12 h-12 mx-auto rounded-2xl bg-green-500/12 border border-green-500/25 text-green-400 grid place-items-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                        <p class="text-white font-semibold">You're all caught up</p>
                        <p class="text-gray-400 text-sm mt-1">No pending tasks right now.</p>
                    </div>
                @endforelse
            </div>

            {{-- Recently completed --}}
            <div class="glass p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-white">Recently Completed</h3>
                    <a href="{{ route('completed_tasks.index') }}" class="text-xs text-gray-400 hover:text-white">Full List &rarr;</a>
                </div>

                @forelse ($recentCompleted as $task)
                    <div class="row-item flex items-center justify-between gap-4 py-3 px-2 -mx-2 rounded-lg">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-200 truncate">{{ $task->task_name }}</p>
                            <p class="text-xs text-gray-500">{{ $task->subject->subject_name }}</p>
                        </div>
                        <span class="chip chip-low">Completed</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-6 border-t border-white/10">Finished tasks will show up here.</p>
                @endforelse
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="flex flex-col gap-3 sm:gap-4">

            {{-- Study sessions --}}
            <div class="glass p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-white">Study Sessions</h3>
                    <a href="{{ route('schedule.index') }}" class="text-xs text-gray-400 hover:text-white">Schedule &rarr;</a>
                </div>

                @forelse ($sessions as $session)
                    @php
                        $diff = (int) today()->diffInDays($session->study_date->copy()->startOfDay(), false);
                        $tag  = $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : $session->study_date->format('D, M d'));
                    @endphp
                    <div class="row-item flex items-start justify-between gap-3 py-3 px-2 -mx-2 rounded-lg">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $session->subject->subject_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ date('g:i A', strtotime($session->start_time)) }} &ndash; {{ date('g:i A', strtotime($session->end_time)) }}</p>
                        </div>
                        <span class="chip {{ $diff === 0 ? 'chip-blue' : 'chip-muted' }} shrink-0">{{ $tag }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 border-t border-white/10">
                        <p class="text-gray-400 text-sm">No study sessions planned.</p>
                        <a href="{{ route('schedule.index') }}" class="inline-block mt-2 text-sm font-semibold text-blue-400 hover:text-blue-300">Plan a session &rarr;</a>
                    </div>
                @endforelse
            </div>

            {{-- Subject overview --}}
            <div class="glass p-4 sm:p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white">Subject Overview</h3>
                    <a href="{{ route('subjects.index') }}" class="text-xs text-gray-400 hover:text-white">
                        {{ $subjects->count() > 5 ? 'See all ' . $subjects->count() : 'Manage' }} &rarr;
                    </a>
                </div>

                @forelse ($topSubjects as $subject)
                    @php
                        $done = $subject->tasks_count - $subject->pending_tasks_count;
                        $pct  = $subject->tasks_count > 0 ? round($done / $subject->tasks_count * 100) : 0;
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between gap-3 text-sm mb-1.5">
                            <span class="text-gray-200 truncate">{{ $subject->subject_name }}</span>
                            <span class="text-xs shrink-0 {{ $subject->tasks_count == 0 ? 'text-gray-600' : ($subject->pending_tasks_count > 0 ? 'text-amber-300' : 'text-green-400') }}">
                                @if ($subject->tasks_count == 0)
                                    No tasks yet
                                @elseif ($subject->pending_tasks_count > 0)
                                    {{ $subject->pending_tasks_count }} pending
                                @else
                                    All done
                                @endif
                            </span>
                        </div>
                        <div class="bar"><i style="width: {{ $pct }}%"></i></div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 text-sm py-6">No subjects yet.</p>
                @endforelse
            </div>

        </div>

    </div>

</div>

@push('scripts')
{{-- Refreshes the dashboard by itself when the date changes (midnight),
     so "Due tomorrow" becomes "Due today", "Overdue", etc. without pressing refresh. --}}
<script>
    (function () {
        function today() {
            var d = new Date();
            return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
        }

        var loadedOn = today();

        function check() {
            if (today() !== loadedOn) {
                window.location.reload();
            }
        }

        setInterval(check, 30000);                       // every 30 seconds
        window.addEventListener('focus', check);         // when you come back to the window
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) check();               // when you switch back to the tab
        });

        // Stops a double click on "Done" from sending the form twice
        document.querySelectorAll('form[data-once]').forEach(function (f) {
            f.addEventListener('submit', function () {
                var b = f.querySelector('button[type="submit"]');
                if (b) { b.disabled = true; b.style.opacity = '.6'; }
            });
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form[data-once] button[type="submit"]').forEach(function (b) {
                b.disabled = false;
                b.style.opacity = '';
            });
        });
    })();
</script>
@endpush

@endsection