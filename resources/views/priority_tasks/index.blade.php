@extends('layouts.app')

@section('title', 'Priority Tasks')

@section('content')

@php
    $all   = collect($tasks);
    $count = $all->count();
    $by    = $all->groupBy(fn ($t) => strtolower($t->priority ?? ''));
    $high  = $by->get('high', collect())->count();
    $med   = $by->get('medium', collect())->count();
    $low   = $by->get('low', collect())->count();
@endphp

@include('partials.page-kit')

<div class="pg">

    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">Priority Tasks</h1>
            <p class="pg-sub">Your pending tasks, from highest to lowest priority.</p>
        </div>
        <span class="pg-pill" style="position:relative;z-index:1;font-size:12px;padding:6px 14px">
            {{ $count }} {{ $count == 1 ? 'task' : 'tasks' }}
        </span>
    </header>

    @if ($count > 0)

        {{-- Priority spectrum: how your pending work is spread --}}
        <div class="pg-panel pad" style="margin-bottom:12px">
            <div class="pg-spectrum" role="img" aria-label="{{ $high }} high, {{ $med }} medium, {{ $low }} low priority">
                @if ($high) <i style="flex:{{ $high }};background:#fb7185"></i> @endif
                @if ($med)  <i style="flex:{{ $med }};background:#fbbf24"></i> @endif
                @if ($low)  <i style="flex:{{ $low }};background:#60a5fa"></i> @endif
            </div>
            <div class="pg-legend">
                <span style="--c:#fb7185">High<b>{{ $high }}</b></span>
                <span style="--c:#fbbf24">Medium<b>{{ $med }}</b></span>
                <span style="--c:#60a5fa">Low<b>{{ $low }}</b></span>
            </div>
        </div>

        <div class="pg-panel pad">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <h3 style="font-family:'Sora',sans-serif;font-size:15px;font-weight:600">Task in order</h3>
            </div>

            <div class="flex flex-col gap-3">

                @foreach ($tasks as $i => $task)

                    @include('partials.task-card', [
                        'task' => $task,
                        'rank' => $i + 1,
                        'rankLabel' => 'Dequeued #',
                        'badge' => $i === 0
                            ? 'Do this first'
                            : $task->priority . ' priority',
                        'badgeClass' => $i === 0
                            ? 'bg-blue-500/15 text-blue-300'
                            : 'bg-white/5 text-gray-400',
                    ])

                @endforeach

            </div>

        </div>

    @else

        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
            <b>No pending tasks</b>
            <p>You don't have any pending tasks.</p>
            <a href="{{ route('tasks.index') }}" class="pg-cta">View Tasks</a>
        </div>

    @endif

</div>

@endsection