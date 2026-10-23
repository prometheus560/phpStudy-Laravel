@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $priorityChip = ['High' => 'chip-high', 'Medium' => 'chip-med', 'Low' => 'chip-low'];
    $dueLabel = function ($deadline) {
        $days = (int) today()->diffInDays($deadline->copy()->startOfDay(), false);
        if ($days < 0) return 'Overdue by ' . abs($days) . 'd';
        if ($days === 0) return 'Due today';
        if ($days === 1) return 'Due tomorrow';
        return $days . ' days left';
    };
    $overallTotal = $stats['pending'] + $stats['completed'];
    $overallPct = $overallTotal > 0 ? (int) round($stats['completed'] / $overallTotal * 100) : 0;
    $todayCount = $sessions->filter(fn ($s) => $s->study_date->isToday())->count();
    $parts = [];
    if ($dueThisWeek > 0) $parts[] = $dueThisWeek . ($dueThisWeek === 1 ? ' task' : ' tasks') . ' due this week';
    if ($todayCount > 0) $parts[] = $todayCount . ($todayCount === 1 ? ' study session' : ' study sessions') . ' today';
    $summary = count($parts) ? 'You have ' . implode(' and ', $parts) . '.' : 'Nothing urgent right now. A good day to get ahead.';
    $topSubjects = $subjects->sortByDesc('pending_tasks_count')->take(5);
@endphp

<style>
    .dash-page{width:100%;max-width:none;margin:0 auto;padding:0 0 28px;color:#f4f4fb}
    .dash-hero{position:relative;isolation:isolate;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:32px;min-height:300px;padding:42px 3.6%;border:1px solid rgba(190,175,255,.12);border-radius:28px;background:rgba(14,13,30,.66);margin-bottom:20px}
    .dash-hero:before,.dash-hero:after{content:"";position:absolute;z-index:-1;top:-55%;height:210%;border:1px solid rgba(190,175,255,.08);border-radius:50%;pointer-events:none}
    .dash-hero:before{right:22%;width:60%}.dash-hero:after{right:5%;width:43%}
    .dash-hero-copy{min-width:0}.dash-eyebrow{margin-bottom:10px;color:#9ca3bd;font-size:12px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
    .dash-hero h1{font-size:clamp(38px,5vw,68px);font-weight:800;letter-spacing:-.045em;line-height:1.04;color:#fff;margin:0}
    .dash-hero h1 span{display:block;background:linear-gradient(90deg,#60a5fa,#a78bfa 62%,#d8a8ff);-webkit-background-clip:text;background-clip:text;color:transparent;overflow-wrap:anywhere}
    .dash-hero p{margin-top:20px;color:#9aa0b8;font-size:18px}
    .dash-progress{position:relative;flex:0 0 234px;width:234px;height:234px;display:grid;place-items:center}
    .dash-progress svg{position:absolute;inset:0;width:100%;height:100%;transform:rotate(-90deg)}
    .dash-progress .progress-track{fill:none;stroke:rgba(255,255,255,.075);stroke-width:10}
    .dash-progress .progress-value{fill:none;stroke:url(#dashGradient);stroke-width:10;stroke-linecap:round;stroke-dasharray:289.03;transition:stroke-dashoffset .4s ease}
    .dash-progress-copy{position:relative;text-align:center}.dash-progress-copy strong{display:block;font-size:52px;line-height:1;font-weight:800;letter-spacing:-.04em}.dash-progress-copy span{display:block;margin-top:8px;color:#9298b0;font-size:14px}
    .dash-stat-strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));overflow:hidden;border:1px solid rgba(190,175,255,.12);border-radius:22px;background:rgba(14,13,30,.66);margin-bottom:20px}
    .dash-stat{position:relative;padding:27px 28px 24px;border-left:1px solid rgba(190,175,255,.10);min-width:0}
    .dash-stat:first-child{border-left:0}.dash-stat:before{content:"";position:absolute;left:0;top:22px;bottom:22px;width:3px;border-radius:3px;background:var(--stat-color)}
    .dash-stat-value{display:block;font-size:54px;line-height:1;font-weight:800;letter-spacing:-.04em;color:#f8f8ff}
    .dash-stat-label{display:flex;align-items:center;gap:10px;margin-top:15px;color:#a6abc1;font-size:16px}.dash-stat-label svg{width:20px;height:20px;color:var(--stat-color);flex-shrink:0}
    .dash-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(300px,1fr);gap:20px}
    .dash-column{display:flex;flex-direction:column;gap:20px;min-width:0}.dash-panel{min-width:0;padding:25px 27px;border:1px solid rgba(190,175,255,.12);border-radius:27px;background:rgba(14,13,30,.68);box-shadow:inset 0 1px 0 rgba(255,255,255,.025)}
    .dash-panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding-bottom:17px;border-bottom:1px solid rgba(190,175,255,.12);margin-bottom:8px}
    .dash-panel-title{display:flex;align-items:center;gap:12px;min-width:0;font-size:22px;font-weight:750;letter-spacing:-.025em;color:#f3f3fb}
    .dash-panel-link{flex-shrink:0;color:#8f96b0;font-size:15px;text-decoration:none;white-space:nowrap}.dash-panel-link:hover{color:#fff}
    .dash-count{font-family:Inter,sans-serif;font-size:13px;font-weight:700;padding:5px 12px;border-radius:99px;background:rgba(59,130,246,.13);color:#7db4ff;white-space:nowrap}
    .dash-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:15px 3px;border-bottom:1px solid rgba(190,175,255,.08)}.dash-row:last-child{border-bottom:0}
    .dash-row-main{min-width:0}.dash-row-name{font-size:15px;font-weight:600;color:#f2f2fa;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.dash-row-sub{margin-top:5px;font-size:13px;color:#858ca6}
    .dash-row-side{display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-shrink:0}.dash-badge{display:inline-flex;align-items:center;justify-content:center;padding:5px 11px;border-radius:99px;border:1px solid rgba(190,175,255,.13);background:rgba(255,255,255,.035);font-size:12px;color:#b7bdd1;white-space:nowrap}.dash-badge.today{color:#7db4ff;border-color:rgba(96,165,250,.4);background:rgba(59,130,246,.07)}
    .dash-empty{text-align:center;padding:35px 10px 27px}.dash-empty strong{display:block;font-size:20px;color:#f5f5fb;font-weight:750}.dash-empty p{margin-top:8px;color:#8f96b0;font-size:15px}
    .dash-subject{margin:16px 0 18px}.dash-subject:last-child{margin-bottom:0}.dash-subject-line{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:9px;font-size:14px}.dash-subject-name{color:#d9dbea;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.dash-subject-status{font-size:13px;color:#858ca6;white-space:nowrap}.dash-bar{height:7px;border-radius:99px;background:rgba(255,255,255,.07);overflow:hidden}.dash-bar span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,#3b82f6,#8b5cf6)}
    .dash-notice{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 18px;margin-bottom:18px;border:1px solid rgba(251,191,36,.25);border-radius:15px;background:rgba(251,191,36,.07);color:#fcd34d;text-decoration:none}
    .dash-notice strong{font-weight:700}
    @media(max-width:1050px){.dash-hero{padding:34px 28px;min-height:260px}.dash-progress{width:190px;height:190px;flex-basis:190px}.dash-progress-copy strong{font-size:43px}.dash-stat{padding:23px 20px}.dash-stat-value{font-size:46px}.dash-grid{grid-template-columns:minmax(0,1.5fr) minmax(260px,1fr);gap:14px}.dash-column{gap:14px}.dash-panel{padding:21px}}
    @media(max-width:760px){.dash-page{padding-bottom:18px}.dash-hero{min-height:0;align-items:flex-start;flex-direction:column;gap:24px;padding:28px 22px;border-radius:22px}.dash-hero h1{font-size:clamp(34px,9vw,52px)}.dash-hero p{font-size:15px;margin-top:14px}.dash-progress{width:148px;height:148px;flex-basis:auto;align-self:center}.dash-progress-copy strong{font-size:34px}.dash-progress-copy span{font-size:12px}.dash-stat-strip{grid-template-columns:repeat(2,minmax(0,1fr));border-radius:18px}.dash-stat{padding:21px 19px;border-top:1px solid rgba(190,175,255,.10)}.dash-stat:nth-child(1),.dash-stat:nth-child(2){border-top:0}.dash-stat:nth-child(3){border-left:0}.dash-stat-value{font-size:42px}.dash-stat-label{font-size:14px;margin-top:11px}.dash-grid{grid-template-columns:minmax(0,1fr);gap:14px}.dash-column{gap:14px}.dash-panel{padding:19px 17px;border-radius:20px}.dash-panel-title{font-size:18px;gap:8px}.dash-panel-link{font-size:12px}.dash-count{font-size:11px;padding:4px 8px}.dash-row{align-items:flex-start}.dash-row-side{gap:6px}.dash-badge{font-size:11px;padding:4px 8px}.dash-notice{font-size:13px}}
    @media(max-width:390px){.dash-stat{padding:18px 13px}.dash-stat-value{font-size:36px}.dash-stat-label{font-size:12px;gap:7px}.dash-panel-head{align-items:flex-start}.dash-row{flex-wrap:wrap}.dash-row-side{justify-content:flex-start;width:100%}}
    @media(prefers-reduced-motion:reduce){.dash-progress .progress-value{transition:none}}
</style>

<div class="dash-page">
    <section class="dash-hero">
        <div class="dash-hero-copy">
            <div class="dash-eyebrow">Your learning overview</div>
            <h1>{{ $greeting }}<span>{{ $user->name }}</span></h1>
            <p>{{ $summary }}</p>
        </div>
        <div class="dash-progress" style="--progress: {{ $overallPct }}" role="img" aria-label="{{ $overallPct }} percent of tasks completed">
            <svg viewBox="0 0 100 100" aria-hidden="true">
                <defs><linearGradient id="dashGradient" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#60a5fa"/><stop offset="100%" stop-color="#c084fc"/></linearGradient></defs>
                <circle class="progress-track" cx="50" cy="50" r="46"/>
                <circle class="progress-value" cx="50" cy="50" r="46" style="stroke-dashoffset: {{ 289.03 - (289.03 * $overallPct / 100) }}"/>
            </svg>
            <div class="dash-progress-copy"><strong>{{ $overallPct }}%</strong><span>{{ $stats['completed'] }} of {{ $overallTotal }} done</span></div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-ok" role="status">{{ session('success') }}</div>
    @endif

    @if ($dueSoon > 0)
        <a href="{{ route('tasks.index') }}" class="dash-notice">
            <span>{{ $dueSoon }} {{ $dueSoon === 1 ? 'task is' : 'tasks are' }} due soon: <strong>
                @foreach (collect($dueSoonTasks ?? [])->take(2) as $task)
                    {{ \Illuminate\Support\Str::limit($task->task_name, 40) }} ({{ strtolower($dueLabel($task->deadline)) }}){{ !$loop->last ? ', ' : '' }}
                @endforeach
                @if ($dueSoon > 2) and {{ $dueSoon - 2 }} more @endif
            </strong></span><span aria-hidden="true">→</span>
        </a>
    @endif

    <section class="dash-stat-strip" aria-label="Study statistics">
        <a href="{{ route('subjects.index') }}" class="dash-stat" style="--stat-color:#60a5fa;text-decoration:none">