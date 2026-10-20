@extends('layouts.app')

@section('title', 'Upcoming Tasks')

@section('content')

@php
    $taskList = collect($tasks);
    $total = $taskList->count();
@endphp

@include('partials.page-kit')

<div class="pg">

    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">Upcoming Tasks</h1>
            <p class="pg-sub">{{ $total }} {{ $total === 1 ? 'pending task' : 'pending' }}</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="pg-back">&larr; All Tasks</a>
    </header>

    @if ($total > 0)

        <div class="pg-panel">

            @foreach ($taskList as $task)
                @php
                    $priority = strtolower($task->priority ?? 'medium');

                    $dueDate = $task->deadline
                        ? \Illuminate\Support\Carbon::parse($task->deadline)->startOfDay()
                        : null;

                    $today = now()->startOfDay();

                    if (!$dueDate) {
                        $dueLabel = 'No due date';
                    } elseif ($dueDate->lt($today)) {
                        $dueLabel = 'Overdue';
                    } elseif ($dueDate->equalTo($today)) {
                        $dueLabel = 'Due today';
                    } elseif ($dueDate->equalTo($today->copy()->addDay())) {
                        $dueLabel = 'Due tomorrow';
                    } else {
                        $daysLeft = (int) $today->diffInDays($dueDate);
                        $dueLabel = $daysLeft . ' days left';
                    }

                    $state = $dueLabel === 'Overdue' ? 'late' : ($dueLabel === 'Due today' ? 'now' : '');
                @endphp

                <article class="pg-row has-side">
                    <div class="pg-date {{ $state }}">
                        @if ($dueDate)
                            <small>{{ $dueDate->format('M') }}</small><b>{{ $dueDate->format('d') }}</b>
                        @else
                            <small>No</small><b>&ndash;</b>
                        @endif
                    </div>

                    <div style="min-width:0">
                        <h2 class="pg-name">{{ $task->task_name }}</h2>
                        <div class="pg-meta">
                            @if ($task->subject)
                                <span class="pg-pill">{{ $task->subject->subject_name }}</span>
                            @endif
                            <span class="pg-pill pg-p-{{ $priority }}">{{ ucfirst($priority) }} Priority</span>
                        </div>
                    </div>

                    <div class="pg-side">
                        <b>@if ($dueDate){{ $dueDate->format('M d, Y') }}@else Not set @endif</b>
                        <span class="{{ $state }}">{{ $dueLabel }}</span>
                    </div>
                </article>
            @endforeach

        </div>

    @else

        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="m9 15 2 2 4-4"/></svg></span>
            <b>No upcoming tasks</b>
            <p>You have no pending tasks right now.</p>
            <a href="{{ route('tasks.index') }}" class="pg-cta">View Tasks</a>
        </div>

    @endif

</div>

@endsection