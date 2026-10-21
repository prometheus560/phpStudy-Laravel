@extends('layouts.app')

@section('title', 'Study Schedule')

@section('content')

@php
    $today  = today()->format('Y-m-d');
    $sorted = $schedules->sortBy(fn ($s) => $s->study_date->format('Y-m-d') . ' ' . $s->start_time)->values();
    $groups = $sorted->groupBy(fn ($s) => $s->study_date->format('Y-m-d'));

    $sections = [
        'Upcoming' => $groups->filter(fn ($g, $k) => $k >= $today),
        'Past'     => $groups->filter(fn ($g, $k) => $k < $today)->reverse(),
    ];

    $upcomingCount = $sections['Upcoming']->sum(fn ($g) => $g->count());
    $pastCount     = $sections['Past']->sum(fn ($g) => $g->count());

    $minsOf = fn ($s) => max(0, (int) ((strtotime($s->end_time) - strtotime($s->start_time)) / 60));

    $weekStart = today()->startOfWeek()->format('Y-m-d');
    $weekEnd   = today()->endOfWeek()->format('Y-m-d');
    $weekMins  = $sorted
        ->filter(fn ($s) => $s->study_date->format('Y-m-d') >= $weekStart && $s->study_date->format('Y-m-d') <= $weekEnd)
        ->sum(fn ($s) => $minsOf($s));
    $weekHours = rtrim(rtrim(number_format($weekMins / 60, 1), '0'), '.');

    $durText = function ($mins) {
        return $mins >= 60 ? intdiv($mins, 60) . 'h' . ($mins % 60 ? ' ' . ($mins % 60) . 'm' : '') : $mins . 'm';
    };

    // One colour per subject, used in the calendar and the list
    $palette = ['#60a5fa', '#a78bfa', '#34d399', '#fbbf24', '#fb7185', '#22d3ee', '#f472b6', '#a3e635'];
    $colorOf = [];
    foreach ($subjects->values() as $i => $sub) { $colorOf[$sub->id] = $palette[$i % count($palette)]; }

    // Pending task deadlines (shown on the calendar). Works even if the controller doesn't send them.
    $deadlines = isset($tasks) ? collect($tasks) : collect();

    $payload = [
        'sessions' => $sorted->map(fn ($s) => [
            'id'         => $s->id,
            'subject_id' => $s->subject_id,
            'subject'    => $s->subject->subject_name,
            'date'       => $s->study_date->format('Y-m-d'),
            'start'      => substr($s->start_time, 0, 5),
            'end'        => substr($s->end_time, 0, 5),
            'color'      => $colorOf[$s->subject_id] ?? '#60a5fa',
        ])->values(),
        'tasks' => $deadlines->filter(fn ($t) => $t->deadline)->map(fn ($t) => [
            'name'     => $t->task_name,
            'subject'  => $t->subject?->subject_name ?? '',
            'date'     => \Illuminate\Support\Carbon::parse($t->deadline)->format('Y-m-d'),
            'priority' => $t->priority,
        ])->values(),
        'base' => url('/schedule'),
    ];
@endphp

@include('partials.page-kit')

<style>
    .sc-tabs { display: inline-flex; padding: 4px; gap: 4px; border: 1px solid var(--pg-line); border-radius: 14px; background: rgba(255,255,255,.03); }
    .sc-tabs button { height: 34px; padding: 0 16px; border: 0; border-radius: 10px; background: none; color: var(--pg-dim); font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; }
    .sc-tabs button.on { background: rgba(139,92,246,.22); color: #e4dcff; }

    /* Live now / next card */
    .sc-now { position: relative; overflow: hidden; display: grid; gap: 14px; margin-bottom: 14px; padding: 20px 22px; border: 1px solid var(--pg-line); border-radius: 22px 22px 6px 22px; background: var(--pg-panel); }
    .sc-now::after { content: ""; position: absolute; right: -60px; top: -60px; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, var(--c, #3b82f6), transparent 70%); opacity: .22; pointer-events: none; }
    .sc-now small { font-size: 12px; color: var(--pg-dim); }
    .sc-now h3 { font-family: 'Sora', sans-serif; font-size: clamp(20px, 4.5vw, 28px); font-weight: 700; letter-spacing: -.03em; line-height: 1.1; margin: 6px 0 4px; }
    .sc-now p { font-size: 13.5px; color: #b9bdd0; }
    .sc-live { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: #7dd3fc; }
    .sc-live i { width: 8px; height: 8px; border-radius: 50%; background: #38bdf8; animation: scPing 2s infinite; }
    @keyframes scPing { 70% { box-shadow: 0 0 0 8px rgba(56,189,248,0); } 0% { box-shadow: 0 0 0 0 rgba(56,189,248,.6); } }

    /* Week navigation */
    .sc-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; }
    .sc-nav { display: inline-flex; align-items: center; gap: 8px; }
    .sc-nav button { height: 36px; min-width: 36px; padding: 0 12px; border-radius: 11px; border: 1px solid var(--pg-line); background: rgba(255,255,255,.04); color: #cfd2e0; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; }
    .sc-nav button:hover { color: #fff; background: rgba(255,255,255,.09); }
    .sc-range { font-family: 'Sora', sans-serif; font-size: 15px; font-weight: 600; }

    /* Week grid (PC) */
    .sc-cal { border: 1px solid var(--pg-line); border-radius: 20px; background: var(--pg-panel); overflow: hidden; }
    .sc-row { display: grid; grid-template-columns: 50px repeat(7, minmax(0, 1fr)); }
    .sc-hd > div { padding: 10px 4px 9px; text-align: center; border-left: 1px solid var(--pg-line); border-bottom: 1px solid var(--pg-line); }
    .sc-hd > div:first-child { border-left: 0; }
    .sc-hd b { display: block; font-family: 'Sora', sans-serif; font-size: 18px; margin-top: 2px; }
    .sc-hd small { font-size: 11px; color: var(--pg-dim); text-transform: uppercase; letter-spacing: .06em; }
    .sc-hd .today b { color: #7dd3fc; } .sc-hd .today { background: rgba(56,189,248,.07); }
    .sc-due { display: inline-block; margin-top: 4px; padding: 1px 8px; border-radius: 99px; font-size: 10.5px; font-weight: 600; color: #fbbf24; background: rgba(251,191,36,.12); }
    .sc-gut { position: relative; }
    .sc-gut span { position: absolute; right: 8px; transform: translateY(-50%); font-size: 10.5px; color: #6f748c; }
    .sc-col { position: relative; border-left: 1px solid var(--pg-line); background: linear-gradient(rgba(160,170,255,.08) 1px, transparent 1px) 0 0 / 100% var(--hh); cursor: copy; }
    .sc-col.today { background-color: rgba(56,189,248,.04); }
    .sc-col.past { opacity: .55; }
    .sc-block { position: absolute; left: 3px; right: 3px; overflow: hidden; padding: 5px 8px; text-align: left; border: 0; border-left: 3px solid var(--c); border-radius: 9px; background: linear-gradient(90deg, color-mix(in srgb, var(--c) 24%, #0e0c20), #0e0c20); color: #f2f3fb; font: inherit; cursor: pointer; transition: transform .15s; }
    .sc-block:hover { transform: scale(1.02); z-index: 3; }
    .sc-block b { display: block; font-size: 12px; font-weight: 600; line-height: 1.2; }
    .sc-block span { font-size: 10.5px; color: #b9bdd0; }
    .sc-nowline { position: absolute; left: 0; right: 0; height: 2px; background: #38bdf8; box-shadow: 0 0 10px #38bdf8; z-index: 4; pointer-events: none; }
    .sc-nowline::before { content: ""; position: absolute; left: -4px; top: -3px; width: 8px; height: 8px; border-radius: 50%; background: #38bdf8; }

    /* Day agenda (phone) */
    .sc-strip { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin-bottom: 12px; }
    .sc-chip { position: relative; padding: 8px 0; border: 1px solid var(--pg-line); border-radius: 14px; background: rgba(255,255,255,.03); color: var(--pg-dim); font: inherit; text-align: center; cursor: pointer; }
    .sc-chip small { display: block; font-size: 10px; text-transform: uppercase; }
    .sc-chip b { display: block; font-family: 'Sora', sans-serif; font-size: 16px; color: #e4e6f2; }
    .sc-chip.on { border-color: rgba(167,139,250,.6); background: rgba(139,92,246,.18); }
    .sc-chip.today b { color: #7dd3fc; }
    .sc-chip i { position: absolute; bottom: 4px; left: 50%; width: 5px; height: 5px; margin-left: -2.5px; border-radius: 50%; background: #a78bfa; }
    .sc-card { display: grid; grid-template-columns: 74px 1fr auto; gap: 12px; align-items: center; margin-bottom: 8px; padding: 14px; border: 1px solid var(--pg-line); border-left: 3px solid var(--c); border-radius: 16px; background: var(--pg-panel); }
    .sc-card .t b { display: block; font-size: 14px; } .sc-card .t span { font-size: 11.5px; color: var(--pg-dim); }
    .sc-card .n { font-size: 14px; font-weight: 600; } .sc-card .n small { display: block; margin-top: 3px; font-size: 11.5px; font-weight: 400; color: var(--pg-dim); }
    .sc-todo { margin-bottom: 8px; padding: 10px 14px; border-radius: 14px; font-size: 12.5px; color: #fcd34d; background: rgba(251,191,36,.08); border: 1px solid rgba(251,191,36,.25); }

    /* List view */
    .sc-day { margin: 18px 4px 8px; display: flex; align-items: center; gap: 10px; font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 600; }
    .sc-day em { font-style: normal; font-family: 'Inter', sans-serif; font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 99px; background: rgba(56,189,248,.15); color: #7dd3fc; }
    .sc-lrow { display: grid; grid-template-columns: 92px 1fr auto; gap: 14px; align-items: center; margin-bottom: 8px; padding: 14px 16px; border: 1px solid var(--pg-line); border-left: 3px solid var(--c); border-radius: 16px; background: var(--pg-panel); }
    .sc-lrow .t b { display: block; font-size: 14px; } .sc-lrow .t span { font-size: 11.5px; color: var(--pg-dim); }
    .sc-lrow .n { font-weight: 600; font-size: 14.5px; }
    .sc-legend { display: flex; flex-wrap: wrap; gap: 6px 16px; margin-top: 12px; font-size: 12px; color: var(--pg-dim); }
    .sc-legend span::before { content: ""; display: inline-block; width: 8px; height: 8px; margin-right: 6px; border-radius: 50%; background: var(--c); }
    .pgd .time-error { color: #fca5a5; font-size: 13px; }
    .pgd .two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width: 560px) { .sc-lrow { grid-template-columns: 1fr auto; } .sc-lrow .t { grid-column: 1 / -1; } .sc-card { grid-template-columns: 64px 1fr; } .sc-card .a { grid-column: 1 / -1; } }
</style>

<div class="pg" style="max-width:1240px">

    {{-- Header --}}
    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">Study Schedule</h1>
            <p class="pg-sub">Plan your study time and stay organized.</p>
        </div>

        <div class="pg-actions">
            <div class="sc-tabs" role="tablist">
                <button type="button" data-view="week" class="on">Week</button>
                <button type="button" data-view="list">List</button>
            </div>

            @if ($subjects->count() > 0)
                <button type="button" data-open-add class="pg-btn main">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Session
                </button>
            @else
                <a href="{{ route('subjects.index') }}" class="pg-btn main">Add Subject first</a>
            @endif
        </div>
    </header>

    @if (session('success'))<div class="pg-flash" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="pg-flash err">{{ $errors->first() }}</div>@endif

    {{-- Live: what is happening now / what is next --}}
    <section class="sc-now" id="sc-now" aria-live="polite"><small>Checking your day...</small></section>

    {{-- Readout strip --}}
    <div class="pg-stats">
        <div class="pg-stat" style="--c:#60a5fa"><b>{{ $upcomingCount }}</b><span>Upcoming {{ $upcomingCount == 1 ? 'session' : 'sessions' }}</span></div>
        <div class="pg-stat" style="--c:#fb923c"><b>{{ $weekHours === '' ? '0' : $weekHours }}h</b><span>Planned this week</span></div>
        <div class="pg-stat" style="--c:#4ade80"><b>{{ $deadlines->count() }}</b><span>Task deadlines to plan around</span></div>
    </div>

    @if ($schedules->count() === 0 && $subjects->count() === 0)

        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <b>No subjects available</b>
            <p>You need to create a subject before creating a schedule.</p>
            <a href="{{ route('subjects.index') }}" class="pg-cta">Add Subject</a>
        </div>

    @else

        {{-- WEEK VIEW --}}
        <section id="view-week">
            <div class="sc-bar">
                <div class="sc-nav">
                    <button type="button" id="wk-prev" aria-label="Previous week">&lsaquo;</button>
                    <button type="button" id="wk-today">Today</button>
                    <button type="button" id="wk-next" aria-label="Next week">&rsaquo;</button>
                </div>
                <span class="sc-range" id="wk-range"></span>
            </div>

            <div id="wk-body"></div>

            <div class="sc-legend">
                @foreach ($subjects as $subject)
                    <span style="--c:{{ $colorOf[$subject->id] }}">{{ $subject->subject_name }}</span>
                @endforeach
            </div>
            <p style="margin-top:10px;font-size:12px;color:#6f748c">Tip: click an empty spot in a day to add a session at that time.</p>
        </section>

        {{-- LIST VIEW --}}
        <section id="view-list" hidden>
            @if ($schedules->count() > 0)
                @foreach ($sections as $kind => $set)
                    @if ($set->count() > 0)

                        @if ($kind === 'Past')
                            <details style="margin-top:22px">
                                <summary style="cursor:pointer;font-size:13px;font-weight:600;color:var(--pg-dim)">Past sessions ({{ $pastCount }})</summary>
                        @endif

                        @foreach ($set as $date => $items)
                            @php
                                $d    = \Carbon\Carbon::parse($date)->startOfDay();
                                $diff = (int) today()->diffInDays($d, false);
                                $tag  = $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : null);
                            @endphp

                            <div style="{{ $kind === 'Past' ? 'opacity:.6' : '' }}">
                                <div class="sc-day">{{ $d->format('l, F d, Y') }} @if ($tag)<em>{{ $tag }}</em>@endif</div>

                                @foreach ($items as $schedule)
                                    @php $mins = $minsOf($schedule); @endphp
                                    <div class="sc-lrow" style="--c:{{ $colorOf[$schedule->subject_id] ?? '#60a5fa' }}">
                                        <div class="t">
                                            <b>{{ date('g:i A', strtotime($schedule->start_time)) }}</b>
                                            <span>to {{ date('g:i A', strtotime($schedule->end_time)) }}</span>
                                        </div>
                                        <div>
                                            <p class="n">{{ $schedule->subject->subject_name }}</p>
                                            @if ($mins > 0)<span class="pg-pill">{{ $durText($mins) }}</span>@endif
                                        </div>
                                        <div style="display:flex;gap:8px">
                                            <button type="button" class="edit-btn pg-act edit"
                                                    data-session="{{ json_encode(['id' => $schedule->id, 'subject_id' => $schedule->subject_id, 'date' => $schedule->study_date->format('Y-m-d'), 'start' => substr($schedule->start_time, 0, 5), 'end' => substr($schedule->end_time, 0, 5)]) }}">Edit</button>

                                            <form method="POST" action="{{ route('schedule.destroy', $schedule) }}" onsubmit="return confirm('Delete this study session?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="pg-act del">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        @if ($kind === 'Past')</details>@endif
                    @endif
                @endforeach
            @else
                <div class="pg-panel pg-empty">
                    <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <b>No study sessions yet</b>
                    <p>Add your first session to start planning your time.</p>
                    <button type="button" data-open-add class="pg-btn main" style="margin-top:18px">+ Add Session</button>
                </div>
            @endif
        </section>

    @endif

</div>

@if ($subjects->count() > 0)

{{-- Add dialog --}}
<dialog id="add-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" action="{{ route('schedule.store') }}" class="time-form pgd">
        @csrf

        <div>
            <h3>Add study session</h3>
            <p class="s">Pick a subject, a date, and the time you want to study.</p>
        </div>

        <div>
            <label for="subject_id">Subject</label>
            <select id="subject_id" name="subject_id" required class="pg-input">
                <option value="">Select subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="study_date">Study date</label>
            <input id="study_date" type="date" name="study_date" value="{{ old('study_date') }}" required class="pg-input">
        </div>

        <div class="two">
            <div>
                <label for="start_time">Start time</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required class="pg-input">
            </div>
            <div>
                <label for="end_time">End time</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required class="pg-input">
            </div>
        </div>

        <div>
            <label for="repeat_weeks">Repeat weekly</label>
            <select id="repeat_weeks" name="repeat_weeks" class="pg-input">
                @foreach ([1 => 'Does not repeat', 4 => 'For 4 weeks', 8 => 'For 8 weeks', 12 => 'For 12 weeks', 16 => 'For 16 weeks'] as $n => $label)
                    <option value="{{ $n }}" @selected((int) old('repeat_weeks', 1) === $n)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <p class="time-error" hidden></p>

        <div class="row">
            <button type="button" id="add-cancel" class="pg-btn ghost">Cancel</button>
            <button type="submit" class="pg-btn main">+ Add Session</button>
        </div>
    </form>
</dialog>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="time-form pgd">
        @csrf
        @method('PATCH')

        <h3>Edit study session</h3>

        <div>
            <label for="e_subject">Subject</label>
            <select id="e_subject" name="subject_id" required class="pg-input">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="e_date">Study date</label>
            <input id="e_date" type="date" name="study_date" required class="pg-input">
        </div>

        <div class="two">
            <div>
                <label for="e_start">Start time</label>
                <input id="e_start" type="time" name="start_time" required class="pg-input">
            </div>
            <div>
                <label for="e_end">End time</label>
                <input id="e_end" type="time" name="end_time" required class="pg-input">
            </div>
        </div>

        <p class="time-error" hidden></p>

        <div class="row" style="justify-content:space-between">
            <button type="submit" form="delete-form" id="e_delete" class="pg-btn danger" onclick="return confirm('Delete this study session?');">Delete</button>
            <span style="display:flex;gap:10px">
                <button type="button" id="edit-cancel" class="pg-btn ghost">Cancel</button>
                <button type="submit" class="pg-btn main">Save changes</button>
            </span>
        </div>
    </form>
</dialog>

<form id="delete-form" method="POST" hidden>@csrf @method('DELETE')</form>

<script>
(() => {
    const D = @json($payload);
    const S = D.sessions, T = D.tasks;
    const HH = 52;                       // pixels per hour in the week grid
    const DAYS = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    const pad = n => String(n).padStart(2, '0');
    const iso = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    const parse = s => new Date(s + 'T00:00:00');
    const mins = t => { const p = t.split(':'); return (+p[0]) * 60 + (+p[1]); };
    const fmt = t => { let h = +t.split(':')[0]; const m = t.split(':')[1]; const ap = h >= 12 ? 'PM' : 'AM'; h = h % 12 || 12; return h + ':' + m + ' ' + ap; };
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const monday = d => { const x = new Date(d); x.setDate(x.getDate() - ((x.getDay() + 6) % 7)); x.setHours(0,0,0,0); return x; };
    const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate() + n); return x; };
    const nice = m => { const h = Math.floor(m / 60), r = m % 60; return h ? h + 'h' + (r ? ' ' + r + 'm' : '') : r + 'm'; };

    let weekStart = monday(new Date());
    let selDay = iso(new Date());

    const add  = document.getElementById('add-dialog');
    const dlg  = document.getElementById('edit-dialog');
    const form = document.getElementById('edit-form');
    const body = document.getElementById('wk-body');

    /* ---------- Add / edit dialogs ---------- */
    function openAdd(date, start, end) {
        if (date)  document.getElementById('study_date').value = date;
        if (start) document.getElementById('start_time').value = start;
        if (end)   document.getElementById('end_time').value = end;
        add.querySelector('.time-error').hidden = true;
        add.showModal();
    }
    function openEdit(s) {
        form.action = D.base + '/' + s.id;
        document.getElementById('delete-form').action = D.base + '/' + s.id;
        document.getElementById('e_subject').value = s.subject_id;
        document.getElementById('e_date').value    = s.date;
        document.getElementById('e_start').value   = s.start;
        document.getElementById('e_end').value     = s.end;
        form.querySelector('.time-error').hidden = true;
        dlg.showModal();
    }

    document.querySelectorAll('[data-open-add]').forEach(b => b.addEventListener('click', () => openAdd(iso(new Date()) )));
    document.getElementById('add-cancel').addEventListener('click', () => add.close());
    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    add.addEventListener('click', e => { if (e.target === add) add.close(); });
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => openEdit(JSON.parse(b.dataset.session))));

    /* ---------- Clash check + double-click guard ---------- */
    function clash(date, start, end, ignoreId) {
        return S.find(s => s.date === date && s.id !== ignoreId && mins(s.start) < mins(end) && mins(s.end) > mins(start));
    }
    document.querySelectorAll('.time-form').forEach(f => f.addEventListener('submit', e => {
        if (e.submitter && e.submitter.id === 'e_delete') return;
        const date = f.querySelector('[name="study_date"]').value;
        const s = f.querySelector('[name="start_time"]').value;
        const t = f.querySelector('[name="end_time"]').value;
        const err = f.querySelector('.time-error');
        let msg = '';

        if (s && t && t <= s) {
            msg = 'End time must be later than the start time.';
        } else if (date && s && t) {
            const ignore = f.id === 'edit-form' ? +(f.action.split('/').pop()) : 0;
            const c = clash(date, s, t, ignore);
            if (c) msg = 'That overlaps with ' + c.subject + ' (' + fmt(c.start) + ' to ' + fmt(c.end) + ').';
        }

        err.hidden = !msg;
        err.textContent = msg;
        if (msg) { e.preventDefault(); return; }

        const btn = f.querySelector('button[type="submit"]:not(#e_delete):last-of-type');
        if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    }));
    document.querySelectorAll('.time-form button[type="submit"]:not(#e_delete)').forEach(b => { b.dataset.label = b.textContent.trim(); });
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('.time-form button[type="submit"]:not(#e_delete)').forEach(b => { b.disabled = false; b.textContent = b.dataset.label; });
    });

    /* ---------- Tabs ---------- */
    const vw = document.getElementById('view-week'), vl = document.getElementById('view-list');
    function setView(v) {
        if (vw) vw.hidden = v !== 'week';
        if (vl) vl.hidden = v !== 'list';
        document.querySelectorAll('.sc-tabs button').forEach(b => b.classList.toggle('on', b.dataset.view === v));
        try { localStorage.setItem('scView', v); } catch (e) {}
    }
    document.querySelectorAll('.sc-tabs button').forEach(b => b.addEventListener('click', () => setView(b.dataset.view)));
    let saved = null; try { saved = localStorage.getItem('scView'); } catch (e) {}
    setView(saved === 'list' ? 'list' : 'week');

    /* ---------- Week calendar ---------- */
    const wide = () => window.matchMedia('(min-width: 900px)').matches;
    const tasksOn = d => T.filter(t => t.date === d);

    function renderWeek() {
        if (!body) return;
        const days = [...Array(7)].map((_, i) => addDays(weekStart, i));
        const todayIso = iso(new Date());
        const a = days[0], b = days[6];
        document.getElementById('wk-range').textContent =
            MONTHS[a.getMonth()] + ' ' + a.getDate() + ' - ' + (a.getMonth() !== b.getMonth() ? MONTHS[b.getMonth()] + ' ' : '') + b.getDate() + ', ' + b.getFullYear();

        if (!wide()) return renderAgenda(days, todayIso);

        const inWeek = S.filter(s => s.date >= iso(a) && s.date <= iso(b));
        let h0 = 7, h1 = 22;
        inWeek.forEach(s => { h0 = Math.min(h0, Math.floor(mins(s.start) / 60)); h1 = Math.max(h1, Math.ceil(mins(s.end) / 60)); });
        const height = (h1 - h0) * HH;

        let head = '<div class="sc-row sc-hd"><div></div>';
        days.forEach(d => {
            const k = iso(d), tk = tasksOn(k);
            head += '<div class="' + (k === todayIso ? 'today' : '') + '"><small>' + DAYS[(d.getDay() + 6) % 7] + '</small><b>' + d.getDate() + '</b>' +
                (tk.length ? '<span class="sc-due" title="' + esc(tk.map(t => t.name).join(', ')) + '">' + tk.length + ' due</span>' : '') + '</div>';
        });
        head += '</div>';

        let gut = '<div class="sc-gut" style="height:' + height + 'px">';
        for (let h = h0; h <= h1; h++) { gut += '<span style="top:' + ((h - h0) * HH) + 'px">' + (h % 12 || 12) + (h >= 12 && h < 24 ? 'p' : 'a') + '</span>'; }
        gut += '</div>';

        let cols = '';
        const nowM = new Date().getHours() * 60 + new Date().getMinutes();
        days.forEach(d => {
            const k = iso(d);
            let inner = '';
            S.filter(s => s.date === k).forEach(s => {
                const top = (mins(s.start) - h0 * 60) / 60 * HH;
                const h = Math.max(24, (mins(s.end) - mins(s.start)) / 60 * HH - 2);
                inner += '<button type="button" class="sc-block" data-id="' + s.id + '" style="top:' + top + 'px;height:' + h + 'px;--c:' + s.color + '">' +
                    '<b>' + esc(s.subject) + '</b><span>' + fmt(s.start) + ' - ' + fmt(s.end) + '</span></button>';
            });
            if (k === todayIso && nowM >= h0 * 60 && nowM <= h1 * 60) inner += '<i class="sc-nowline" style="top:' + ((nowM - h0 * 60) / 60 * HH) + 'px"></i>';
            cols += '<div class="sc-col ' + (k === todayIso ? 'today' : (k < todayIso ? 'past' : '')) + '" data-date="' + k + '" data-h0="' + h0 + '" style="height:' + height + 'px;--hh:' + HH + 'px">' + inner + '</div>';
        });

        body.innerHTML = '<div class="sc-cal">' + head + '<div class="sc-row">' + gut + cols + '</div></div>';
    }

    function renderAgenda(days, todayIso) {
        if (!days.some(d => iso(d) === selDay)) selDay = iso(days[0]);
        let strip = '<div class="sc-strip">';
        days.forEach(d => {
            const k = iso(d);
            strip += '<button type="button" class="sc-chip ' + (k === selDay ? 'on ' : '') + (k === todayIso ? 'today' : '') + '" data-day="' + k + '"><small>' +
                DAYS[(d.getDay() + 6) % 7] + '</small><b>' + d.getDate() + '</b>' + (S.some(s => s.date === k) ? '<i></i>' : '') + '</button>';
        });
        strip += '</div>';

        let list = '';
        tasksOn(selDay).forEach(t => { list += '<div class="sc-todo">Due: ' + esc(t.name) + (t.subject ? ' (' + esc(t.subject) + ')' : '') + '</div>'; });
        const items = S.filter(s => s.date === selDay);
        items.forEach(s => {
            list += '<div class="sc-card" style="--c:' + s.color + '"><div class="t"><b>' + fmt(s.start) + '</b><span>to ' + fmt(s.end) + '</span></div>' +
                '<div class="n">' + esc(s.subject) + '<small>' + nice(mins(s.end) - mins(s.start)) + '</small></div>' +
                '<div class="a"><button type="button" class="pg-act edit" data-edit="' + s.id + '">Edit</button></div></div>';
        });
        if (!items.length) list += '<div class="pg-panel pg-empty" style="padding:28px 12px"><p>Nothing planned for this day.</p></div>';
        list += '<button type="button" class="pg-btn main" data-add-day="' + selDay + '" style="width:100%;margin-top:6px">+ Add session on this day</button>';

        body.innerHTML = strip + list;
    }

    body && body.addEventListener('click', e => {
        const blk = e.target.closest('.sc-block');
        if (blk) { const s = S.find(x => x.id === +blk.dataset.id); if (s) openEdit(s); return; }
        const ed = e.target.closest('[data-edit]');
        if (ed) { const s = S.find(x => x.id === +ed.dataset.edit); if (s) openEdit(s); return; }
        const chip = e.target.closest('.sc-chip');
        if (chip) { selDay = chip.dataset.day; renderWeek(); return; }
        const ad = e.target.closest('[data-add-day]');
        if (ad) { openAdd(ad.dataset.addDay); return; }
        const col = e.target.closest('.sc-col');
        if (col) {
            const y = e.clientY - col.getBoundingClientRect().top;
            const m = Math.round((+col.dataset.h0 * 60 + y / HH * 60) / 30) * 30;
            openAdd(col.dataset.date, pad(Math.floor(m / 60)) + ':' + pad(m % 60), pad(Math.floor((m + 60) / 60) % 24) + ':' + pad(m % 60));
        }
    });

    const go = n => { weekStart = addDays(weekStart, n * 7); selDay = iso(weekStart); renderWeek(); };
    const p = document.getElementById('wk-prev'), n = document.getElementById('wk-next'), t = document.getElementById('wk-today');
    if (p) p.addEventListener('click', () => go(-1));
    if (n) n.addEventListener('click', () => go(1));
    if (t) t.addEventListener('click', () => { weekStart = monday(new Date()); selDay = iso(new Date()); renderWeek(); });
    window.matchMedia('(min-width: 900px)').addEventListener('change', renderWeek);

    /* ---------- Live now / next card ---------- */
    const box = document.getElementById('sc-now');
    function when(ms) {
        const m = Math.round(ms / 60000);
        if (m < 60) return 'in ' + Math.max(m, 1) + ' min';
        if (m < 1440) return 'in ' + nice(m);
        const days = Math.round(m / 1440);
        return 'in ' + days + (days === 1 ? ' day' : ' days');
    }
    function renderNow() {
        const now = new Date();
        const stamp = s => new Date(s.date + 'T' + s.start + ':00');
        const stampEnd = s => new Date(s.date + 'T' + s.end + ':00');
        const cur = S.find(s => stamp(s) <= now && stampEnd(s) > now);
        const nxt = S.filter(s => stamp(s) > now).sort((x, y) => stamp(x) - stamp(y))[0];

        if (cur) {
            const total = stampEnd(cur) - stamp(cur), done = now - stamp(cur);
            box.style.setProperty('--c', cur.color);
            box.innerHTML = '<div><span class="sc-live"><i></i>In session now</span><h3>' + esc(cur.subject) + '</h3><p>' + fmt(cur.start) + ' - ' + fmt(cur.end) + ' &middot; ' + nice(Math.max(1, Math.round((total - done) / 60000))) + ' left</p></div>' +
                '<div class="pg-track"><i style="width:' + Math.min(100, Math.round(done / total * 100)) + '%"></i></div>';
        } else if (nxt) {
            const same = nxt.date === iso(now);
            box.style.setProperty('--c', nxt.color);
            box.innerHTML = '<div><small>Next up</small><h3>' + esc(nxt.subject) + '</h3><p>' +
                (same ? 'Today' : MONTHS[parse(nxt.date).getMonth()] + ' ' + parse(nxt.date).getDate()) + ', ' + fmt(nxt.start) + ' - ' + fmt(nxt.end) + ' &middot; starts ' + when(stamp(nxt) - now) + '</p></div>';
        } else {
            box.style.setProperty('--c', '#6366f1');
            box.innerHTML = '<div><small>Next up</small><h3>Nothing planned</h3><p>Add a study session so your time is protected.</p></div>';
        }
    }

    renderWeek();
    renderNow();
    setInterval(() => { renderNow(); renderWeek(); }, 60000);

    @if ($errors->any() && old('subject_id') && !old('_method'))
        openAdd();
    @endif
})();
</script>

@endif

@endsection