@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $dueLabel = function ($deadline) {
        $days = (int) today()->diffInDays($deadline->copy()->startOfDay(), false);
        if ($days < 0)   return 'Overdue by ' . abs($days) . 'd';
        if ($days === 0) return 'Due today';
        if ($days === 1) return 'Due tomorrow';
        return $days . ' days left';
    };

    $overallTotal = $stats['pending'] + $stats['completed'];
    $overallPct   = $overallTotal > 0 ? (int) round($stats['completed'] / $overallTotal * 100) : 0;
    $ringOffset   = round(326.7 * (1 - $overallPct / 100), 1);

    $todayCount = $sessions->filter(fn ($s) => $s->study_date->isToday())->count();
    $parts = [];
    if ($dueThisWeek > 0) { $parts[] = $dueThisWeek . ($dueThisWeek == 1 ? ' task' : ' tasks') . ' due this week'; }
    if ($todayCount > 0)  { $parts[] = $todayCount . ($todayCount == 1 ? ' study session' : ' study sessions') . ' today'; }
    $summary = count($parts) ? 'You have ' . implode(' and ', $parts) . '.' : 'Nothing urgent right now. A good day to get ahead.';

    $topSubjects = $subjects->sortByDesc('pending_tasks_count')->take(5);
@endphp

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
    .ct { --ct-line: rgba(160,170,255,.14); --ct-panel: rgba(12,11,30,.62); --ct-dim: #8d92ad; --ct-ink: #f2f3fb;
          --ct-hi: #fb7185; --ct-md: #fbbf24; --ct-lo: #4ade80; --ct-blue: #7db4ff; --ct-violet: #a78bfa;
          max-width: 1240px; margin: 0 auto; padding: 22px 16px 96px; color: var(--ct-ink); }
    .ct * { box-sizing: border-box; }
    .ct h2, .ct h3, .ct .ct-num { font-family: 'Sora', 'Inter', system-ui, sans-serif; }
    .ct a { color: inherit; text-decoration: none; }

    /* Hero */
    .ct-hero { position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: center;
               padding: 22px 20px; border: 1px solid var(--ct-line); border-radius: 28px 28px 6px 28px; background: var(--ct-panel); backdrop-filter: blur(14px); }
    .ct-hero::before, .ct-hero::after { content: ""; position: absolute; border-radius: 50%; border: 1px solid var(--ct-line); pointer-events: none; }
    .ct-hero::before { width: 420px; height: 420px; right: -120px; top: -150px; }
    .ct-hero::after  { width: 640px; height: 640px; right: -230px; top: -260px; opacity: .6; }
    .ct-hero h2 { font-size: clamp(26px, 6.2vw, 46px); font-weight: 800; line-height: 1.05; letter-spacing: -.035em; }
    .ct-hero h2 .grad-text { display: inline; }
    .ct-hero p { color: var(--ct-dim); font-size: 14px; margin-top: 10px; max-width: 46ch; }
    .ct-ring { position: relative; width: 104px; height: 104px; z-index: 1; }
    .ct-ring svg { width: 100%; height: 100%; display: block; overflow: visible; }
    .ct-ring .ct-ring-val { position: absolute; inset: 0; display: grid; place-items: center; text-align: center; line-height: 1; }
    .ct-ring .ct-num { font-size: 26px; font-weight: 700; }
    .ct-ring small { display: block; font-size: 10px; color: var(--ct-dim); margin-top: 4px; }

    /* Signal strip */
    .ct-signal { display: flex; align-items: center; gap: 12px; margin-top: 12px; padding: 12px 16px; border-radius: 14px;
                 border: 1px solid rgba(251,191,36,.3); background: linear-gradient(90deg, rgba(251,191,36,.14), rgba(251,191,36,.04)); color: #fcd34d; font-size: 13.5px; }
    .ct-signal i { width: 9px; height: 9px; border-radius: 50%; background: #fbbf24; flex: none; box-shadow: 0 0 0 0 rgba(251,191,36,.6); animation: ctPing 2s infinite; }
    .ct-signal span { flex: 1; min-width: 0; }
    @keyframes ctPing { 70% { box-shadow: 0 0 0 9px rgba(251,191,36,0); } 100% { box-shadow: 0 0 0 0 rgba(251,191,36,0); } }
    .ct-flash { margin-top: 12px; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(74,222,128,.3); background: rgba(74,222,128,.1); color: #86efac; font-size: 13.5px; }

    /* Readout rail: one connected strip instead of four separate cards */
    .ct-rail { display: grid; grid-template-columns: 1fr 1fr; margin-top: 12px; border: 1px solid var(--ct-line); border-radius: 20px; background: var(--ct-panel); overflow: hidden; }
    .ct-cell { position: relative; padding: 16px 16px 14px; border-right: 1px solid var(--ct-line); border-bottom: 1px solid var(--ct-line); transition: background .2s; }
    .ct-cell:nth-child(2n) { border-right: 0; }
    .ct-cell:nth-last-child(-n+2) { border-bottom: 0; }
    .ct-cell:hover { background: rgba(255,255,255,.04); }
    .ct-cell::before { content: ""; position: absolute; left: 0; top: 16px; bottom: 16px; width: 3px; border-radius: 0 3px 3px 0; background: var(--c); }
    .ct-cell .ct-num { font-size: 34px; font-weight: 700; letter-spacing: -.03em; line-height: 1; }
    .ct-cell .lbl { display: flex; align-items: center; gap: 7px; margin-top: 8px; font-size: 12.5px; color: var(--ct-dim); }
    .ct-cell svg { width: 15px; height: 15px; color: var(--c); flex: none; }
    .ct-cell .tag { position: absolute; right: 12px; top: 12px; font-size: 10.5px; font-weight: 600; padding: 2px 8px; border-radius: 99px; background: rgba(244,63,94,.16); color: #fb7185; }

    /* Layout grid */
    .ct-grid { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 12px; }
    .ct-panel { border: 1px solid var(--ct-line); border-radius: 20px; background: var(--ct-panel); backdrop-filter: blur(14px); padding: 18px 16px; min-width: 0; }
    .ct-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
    .ct-head h3 { font-size: 15px; font-weight: 600; letter-spacing: -.01em; }
    .ct-head a { font-size: 12px; color: var(--ct-dim); }
    .ct-head a:hover { color: #fff; }
    .ct-count { font-size: 11px; padding: 2px 9px; border-radius: 99px; background: rgba(125,180,255,.14); color: var(--ct-blue); font-weight: 600; }
    .ct-empty { text-align: center; padding: 26px 8px 10px; border-top: 1px solid var(--ct-line); color: var(--ct-dim); font-size: 13px; }
    .ct-empty b { display: block; color: #fff; font-size: 15px; margin-bottom: 4px; }

    /* Workbench rows */
    .ct-task { display: grid; grid-template-columns: 46px 1fr auto; gap: 12px; align-items: center; padding: 12px 6px; border-top: 1px solid var(--ct-line); border-radius: 10px; transition: background .2s; }
    .ct-task:hover { background: rgba(255,255,255,.03); }
    .ct-date { width: 46px; padding: 6px 0; text-align: center; border-radius: 12px; border: 1px solid var(--ct-line); background: rgba(255,255,255,.03); line-height: 1; }
    .ct-date small { display: block; font-size: 9.5px; color: var(--ct-dim); text-transform: uppercase; letter-spacing: .06em; }
    .ct-date b { display: block; font-family: 'Sora', sans-serif; font-size: 18px; margin-top: 3px; }
    .ct-task.late .ct-date { border-color: rgba(251,113,133,.45); } .ct-task.late .ct-date b { color: var(--ct-hi); }
    .ct-task.now  .ct-date { border-color: rgba(251,191,36,.5); }  .ct-task.now  .ct-date b { color: var(--ct-md); }
    .ct-name { font-size: 14px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ct-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 5px; font-size: 11.5px; color: var(--ct-dim); }
    .ct-meta .late { color: var(--ct-hi); } .ct-meta .now { color: var(--ct-md); }
    .ct-pill { font-size: 10.5px; font-weight: 600; padding: 2px 9px; border-radius: 99px; border: 1px solid var(--ct-line); color: var(--ct-dim); }
    .ct-p-High { color: var(--ct-hi); border-color: rgba(251,113,133,.4); background: rgba(251,113,133,.1); }
    .ct-p-Medium { color: var(--ct-md); border-color: rgba(251,191,36,.4); background: rgba(251,191,36,.1); }
    .ct-p-Low { color: var(--ct-lo); border-color: rgba(74,222,128,.4); background: rgba(74,222,128,.1); }
    .ct-done { height: 36px; padding: 0 14px; border: 1px solid rgba(74,222,128,.4); border-radius: 11px; background: rgba(74,222,128,.1); color: #86efac; font: inherit; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: background .2s, transform .1s; }
    .ct-done:hover { background: rgba(74,222,128,.2); } .ct-done:active { transform: scale(.96); }

    /* Study sessions: vertical timeline */
    .ct-line { position: relative; padding-left: 20px; }
    .ct-line::before { content: ""; position: absolute; left: 4px; top: 8px; bottom: 8px; width: 1px; background: var(--ct-line); }
    .ct-sess { position: relative; padding: 9px 0; display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; }
    .ct-sess::before { content: ""; position: absolute; left: -20px; top: 15px; width: 9px; height: 9px; border-radius: 50%; background: #1b1a36; border: 2px solid var(--ct-dim); }
    .ct-sess.today::before { border-color: var(--ct-blue); background: var(--ct-blue); box-shadow: 0 0 12px rgba(125,180,255,.7); }
    .ct-sess .t { font-size: 13.5px; font-weight: 500; } .ct-sess .s { font-size: 11.5px; color: var(--ct-dim); margin-top: 3px; }

    /* Subject bars */
    .ct-sub { margin-bottom: 14px; } .ct-sub:last-child { margin-bottom: 0; }
    .ct-sub div { display: flex; justify-content: space-between; gap: 10px; font-size: 13px; margin-bottom: 6px; }
    .ct-sub em { font-style: normal; font-size: 11.5px; flex: none; }
    .ct-track { height: 5px; border-radius: 9px; background: rgba(255,255,255,.07); overflow: hidden; }
    .ct-track i { display: block; height: 100%; border-radius: 9px; background: linear-gradient(90deg, #60a5fa, #a78bfa); }

    .ct-recent { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 10px 6px; border-top: 1px solid var(--ct-line); }
    .ct-recent p { font-size: 13.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ct-recent small { font-size: 11.5px; color: var(--ct-dim); }

    @media (max-width: 560px) {
        .ct-task { grid-template-columns: 42px 1fr; }
        .ct-task form { grid-column: 1 / -1; }
        .ct-done { width: 100%; }
        .ct-hero { grid-template-columns: 1fr; }
        .ct-ring { width: 92px; height: 92px; }
    }
    @media (min-width: 900px) {
        .ct { padding: 36px 32px 48px; }
        .ct-hero { padding: 34px 40px; gap: 30px; }
        .ct-ring { width: 168px; height: 168px; }
        .ct-ring .ct-num { font-size: 40px; }
        .ct-rail { grid-template-columns: repeat(4, 1fr); margin-top: 14px; }
        .ct-cell { border-bottom: 0 !important; border-right: 1px solid var(--ct-line) !important; padding: 22px 22px 20px; }
        .ct-cell:last-child { border-right: 0 !important; }
        .ct-cell .ct-num { font-size: 44px; }
        .ct-grid { grid-template-columns: repeat(12, 1fr); gap: 14px; margin-top: 14px; }
        .ct-a { grid-column: 1 / span 8; } .ct-b { grid-column: 9 / span 4; }
        .ct-c { grid-column: 1 / span 5; } .ct-d { grid-column: 6 / span 7; }
        .ct-panel { padding: 22px 24px; }
        .ct-panel.ct-a { border-radius: 6px 28px 28px 28px; }
        .ct-task { grid-template-columns: 54px 1fr auto; gap: 16px; }
        .ct-date { width: 54px; }
    }
    @media (prefers-reduced-motion: reduce) { .ct * { animation: none !important; transition: none !important; } }
</style>

<div class="ct">

    {{-- Hero --}}
    <section class="ct-hero">
        <div style="position:relative;z-index:1">
            <h2 class="text-white">{{ $greeting }},<br><span class="grad-text">{{ $user->name }}</span></h2>
            <p>{{ $summary }}</p>
        </div>
        <div class="ct-ring" role="img" aria-label="Overall progress {{ $overallPct }} percent">
            <svg viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="7"/>
                <defs><linearGradient id="ctg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#60a5fa"/><stop offset="1" stop-color="#c084fc"/></linearGradient></defs>
                <circle cx="60" cy="60" r="52" fill="none" stroke="url(#ctg)" stroke-width="7" stroke-linecap="round"
                        stroke-dasharray="326.7" stroke-dashoffset="{{ $ringOffset }}" transform="rotate(-90 60 60)"/>
                <g transform="rotate({{ $overallPct * 3.6 }} 60 60)"><circle cx="60" cy="8" r="5" fill="#fff"/></g>
            </svg>
            <div class="ct-ring-val"><div><span class="ct-num">{{ $overallPct }}%</span><small>{{ $stats['completed'] }} of {{ $overallTotal }} done</small></div></div>
        </div>
    </section>

    @if (session('success'))
        <div class="ct-flash" role="status">{{ session('success') }}</div>
    @endif

    {{-- Due soon --}}
    @if ($dueSoon > 0)
        <a href="{{ route('tasks.index') }}" class="ct-signal">
            <i></i>
            <span>
                {{ $dueSoon }} {{ $dueSoon == 1 ? 'task is' : 'tasks are' }} due soon:
                <strong>
                    @foreach (collect($dueSoonTasks ?? [])->take(2) as $t)
                        {{ \Illuminate\Support\Str::limit($t->task_name, 40) }} ({{ strtolower($dueLabel($t->deadline)) }}){{ !$loop->last ? ',' : '' }}
                    @endforeach
                    @if ($dueSoon > 2) and {{ $dueSoon - 2 }} more @endif
                </strong>
            </span>
            <span style="flex:none">&rsaquo;</span>
        </a>
    @endif

    {{-- Readout rail --}}
    <nav class="ct-rail" aria-label="Overview">
        <a href="{{ route('subjects.index') }}" class="ct-cell" style="--c:#60a5fa">
            <p class="ct-num">{{ $stats['subjects'] }}</p>
            <p class="lbl"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>Subjects</p>
        </a>
        <a href="{{ route('tasks.index') }}" class="ct-cell" style="--c:#fb923c">
            @if ($stats['overdue'] > 0)<span class="tag">{{ $stats['overdue'] }} overdue</span>@endif
            <p class="ct-num">{{ $stats['pending'] }}</p>
            <p class="lbl"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Pending</p>
        </a>
        <a href="{{ route('completed_tasks.index') }}" class="ct-cell" style="--c:#4ade80">
            <p class="ct-num">{{ $stats['completed'] }}</p>
            <p class="lbl"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Completed</p>
        </a>
        <a href="{{ route('priority_tasks.index') }}" class="ct-cell" style="--c:#fb7185">
            <p class="ct-num">{{ $stats['high'] }}</p>
            <p class="lbl"><svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>High priority</p>
        </a>
    </nav>

    <div class="ct-grid">

        {{-- Workbench --}}
        <section class="ct-panel ct-a">
            <div class="ct-head">
                <div style="display:flex;align-items:center;gap:10px">
                    <h3>Active Workbench</h3>
                    <span class="ct-count">{{ $workbench->count() }} / {{ $stats['pending'] }}</span>
                </div>
                <a href="{{ route('tasks.index') }}">View all &rarr;</a>
            </div>

            @forelse ($workbench as $task)
                @php
                    $days = (int) today()->diffInDays($task->deadline->copy()->startOfDay(), false);
                    $state = $days < 0 ? 'late' : ($days === 0 ? 'now' : '');
                @endphp
                <div class="ct-task {{ $state }}">
                    <div class="ct-date"><small>{{ $task->deadline->format('M') }}</small><b>{{ $task->deadline->format('d') }}</b></div>
                    <div style="min-width:0">
                        <p class="ct-name">{{ $task->task_name }}</p>
                        <div class="ct-meta">
                            <span class="ct-pill">{{ $task->subject->subject_name }}</span>
                            <span class="ct-pill ct-p-{{ $task->priority }}">{{ $task->priority }}</span>
                            <span class="{{ $state }}">{{ $dueLabel($task->deadline) }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('tasks.complete', $task) }}" data-once>
                        @csrf
                        @method('PATCH')
                        <button type="submit" title="Mark as done" class="ct-done">&#10003; Done</button>
                    </form>
                </div>
            @empty
                <div class="ct-empty"><b>You're all caught up</b>No pending tasks right now.</div>
            @endforelse
        </section>

        {{-- Study sessions --}}
        <section class="ct-panel ct-b">
            <div class="ct-head">
                <h3>Study Sessions</h3>
                <a href="{{ route('schedule.index') }}">Schedule &rarr;</a>
            </div>
            @forelse ($sessions as $session)
                @php
                    $diff = (int) today()->diffInDays($session->study_date->copy()->startOfDay(), false);
                    $tag  = $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : $session->study_date->format('D, M d'));
                @endphp
                @if ($loop->first)<div class="ct-line">@endif
                <div class="ct-sess {{ $diff === 0 ? 'today' : '' }}">
                    <div style="min-width:0">
                        <p class="t">{{ $session->subject->subject_name }}</p>
                        <p class="s">{{ date('g:i A', strtotime($session->start_time)) }} &ndash; {{ date('g:i A', strtotime($session->end_time)) }}</p>
                    </div>
                    <span class="ct-pill" style="flex:none;{{ $diff === 0 ? 'color:#7db4ff;border-color:rgba(125,180,255,.4)' : '' }}">{{ $tag }}</span>
                </div>
                @if ($loop->last)</div>@endif
            @empty
                <div class="ct-empty">No study sessions planned.<br><a href="{{ route('schedule.index') }}" style="color:#7db4ff;font-weight:600;display:inline-block;margin-top:8px">Plan a session &rarr;</a></div>
            @endforelse
        </section>

        {{-- Recently completed --}}
        <section class="ct-panel ct-c">
            <div class="ct-head">
                <h3>Recently Completed</h3>
                <a href="{{ route('completed_tasks.index') }}">Full list &rarr;</a>
            </div>
            @forelse ($recentCompleted as $task)
                <div class="ct-recent">
                    <div style="min-width:0"><p>{{ $task->task_name }}</p><small>{{ $task->subject->subject_name }}</small></div>
                    <span class="ct-pill ct-p-Low">Completed</span>
                </div>
            @empty
                <div class="ct-empty">Finished tasks will show up here.</div>
            @endforelse
        </section>

        {{-- Subject overview --}}
        <section class="ct-panel ct-d">
            <div class="ct-head">
                <h3>Subject Overview</h3>
                <a href="{{ route('subjects.index') }}">{{ $subjects->count() > 5 ? 'See all ' . $subjects->count() : 'Manage' }} &rarr;</a>
            </div>
            @forelse ($topSubjects as $subject)
                @php
                    $done = $subject->tasks_count - $subject->pending_tasks_count;
                    $pct  = $subject->tasks_count > 0 ? round($done / $subject->tasks_count * 100) : 0;
                @endphp
                <div class="ct-sub">
                    <div>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $subject->subject_name }}</span>
                        <em style="color:{{ $subject->tasks_count == 0 ? '#5b5f78' : ($subject->pending_tasks_count > 0 ? '#fcd34d' : '#4ade80') }}">
                            @if ($subject->tasks_count == 0) No tasks yet
                            @elseif ($subject->pending_tasks_count > 0) {{ $subject->pending_tasks_count }} pending
                            @else All done @endif
                        </em>
                    </div>
                    <div class="ct-track" style="display:block"><i style="width: {{ $pct }}%"></i></div>
                </div>
            @empty
                <div class="ct-empty">No subjects yet.</div>
            @endforelse
        </section>

    </div>
</div>

@push('scripts')
<script>
    (function () {
        function today() {
            var d = new Date();
            return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
        }
        var loadedOn = today();
        function check() { if (today() !== loadedOn) { window.location.reload(); } }
        setInterval(check, 30000);
        window.addEventListener('focus', check);
        document.addEventListener('visibilitychange', function () { if (!document.hidden) check(); });

        document.querySelectorAll('form[data-once]').forEach(function (f) {
            f.addEventListener('submit', function () {
                var b = f.querySelector('button[type="submit"]');
                if (b) { b.disabled = true; b.style.opacity = '.6'; }
            });
        });
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form[data-once] button[type="submit"]').forEach(function (b) {
                b.disabled = false; b.style.opacity = '';
            });
        });
    })();
</script>
@endpush

@endsection