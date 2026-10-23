@extends('layouts.app')

@section('title', "Today's Checklist")

@section('content')
@php
    $fmt = fn ($t) => date('g:i A', strtotime($t));
    $blocks = $todayPlan
        ? collect($todayPlan['items'])->filter(fn ($i) => in_array($i['type'], ['study', 'buffer'], true))->values()
        : collect();
    $hasAnything = $overdue->isNotEmpty() || $dueToday->isNotEmpty() || $blocks->isNotEmpty() || $soon->isNotEmpty();
@endphp

@include('partials.page-kit')
@include('partials.study-plan-kit')

<div class="pg" data-day="{{ $today->format('Y-m-d') }}">

    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">{{ $mode === 'lite' ? 'Low-energy day' : "Today's checklist" }}</h1>
            <p class="pg-sub">{{ $today->format('l, F j') }}</p>
        </div>

        <a href="{{ route('study_plan.index') }}" class="pg-back">&larr; Weekly plan</a>
    </header>

    <div class="sp-tabs">
        <a href="{{ route('study_plan.today') }}" class="pg-chip {{ $mode === 'full' ? 'on' : '' }}">Full day</a>
        <a href="{{ route('study_plan.today', ['mode' => 'lite']) }}" class="pg-chip {{ $mode === 'lite' ? 'on' : '' }}">Low energy</a>
    </div>

    @if (session('success'))<div class="pg-flash" role="status">{{ session('success') }}</div>@endif

    @if ($mode === 'lite')

        <div class="sp-calm">
            Some days are for doing the minimum. Finish these {{ count($lite['steps']) }} steps (about {{ $lite['total'] }} minutes) and you have kept your streak going. Stopping after that is fine.
        </div>

        <div class="sp-prog" data-progress>
            <div><span><b data-done>0</b> of <b data-total>0</b> done</span><span data-pct>0%</span></div>
            <div class="pg-track"><i data-bar style="width:0%"></i></div>
        </div>

        <div class="sp-list">
            @foreach ($lite['steps'] as $i => $step)
                <label class="sp-check focus" data-item>
                    <input type="checkbox" data-key="lite-{{ $i }}">
                    <span>
                        <span class="sp-what" style="display:block">{{ $step['title'] }}</span>
                        <span class="sp-how" style="display:block">{{ $step['detail'] }}</span>
                    </span>
                    <span class="pg-pill">{{ $step['minutes'] }} min</span>
                </label>
            @endforeach
        </div>

    @elseif (!$hasAnything)

        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
            <b>Nothing planned for today</b>
            <p>No study blocks and no tasks due soon. Enjoy the break, or open the weekly plan.</p>
            <a href="{{ route('study_plan.index') }}" class="pg-cta">Weekly plan</a>
        </div>

    @else

        <div class="sp-prog" data-progress>
            <div><span><b data-done>0</b> of <b data-total>0</b> done</span><span data-pct>0%</span></div>
            <div class="pg-track"><i data-bar style="width:0%"></i></div>
        </div>

        {{-- 1. Urgent tasks first --}}
        @if ($overdue->isNotEmpty() || $dueToday->isNotEmpty())
            <div class="pg-section"><h3>Do these first</h3><small>Overdue and due today</small></div>

            <div class="sp-list">
                @foreach ($overdue->concat($dueToday) as $task)
                    <div class="sp-check hot" data-item>
                        <input type="checkbox" data-key="task-{{ $task->id }}" aria-label="Done: {{ $task->task_name }}">
                        <div>
                            <span class="sp-what" style="display:block">{{ $task->task_name }}</span>
                            <span class="sp-how" style="display:block">{{ $task->subject?->subject_name ?? 'No subject' }} &middot; {{ $task->priority }} priority &middot; {{ $task->due_label }}</span>
                        </div>
                        <form method="POST" action="{{ route('tasks.complete', $task) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="pg-act ok">Mark complete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 2. Study blocks in time order --}}
        @if ($blocks->isNotEmpty())
            <div class="pg-section"><h3>Study blocks</h3><small>Hardest subjects sit in your peak hours</small></div>

            <div class="sp-list">
                @foreach ($blocks as $i => $block)
                    @if ($block['type'] === 'buffer')
                        <label class="sp-check" data-item>
                            <input type="checkbox" data-key="block-{{ $i }}">
                            <span>
                                <span class="sp-what" style="display:block">Buffer: catch up</span>
                                <span class="sp-how" style="display:block">{{ $fmt($block['start']) }} - {{ $fmt($block['end']) }}. Finish anything missed, or rest.</span>
                            </span>
                            <span class="sp-tag buf">Buffer</span>
                        </label>
                    @else
                        <label class="sp-check focus" data-item>
                            <input type="checkbox" data-key="block-{{ $i }}">
                            <span>
                                <span class="sp-what" style="display:block">{{ $block['subject'] }}</span>
                                <span class="sp-how" style="display:block">
                                    {{ $fmt($block['start']) }} - {{ $fmt($block['end']) }}.
                                    @if ($block['task'])
                                        Task: {{ $block['task'] }}
                                    @else
                                        {{ $block['activity'] }}: {{ $block['detail'] }}
                                    @endif
                                </span>
                            </span>
                            <span class="pg-pill">{{ $block['focus_minutes'] }} min</span>
                        </label>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- 3. Heads up --}}
        @if ($soon->isNotEmpty())
            <div class="pg-section"><h3>Coming up</h3><small>Due in the next 3 days</small></div>

            <div class="sp-list">
                @foreach ($soon as $task)
                    <div class="sp-check" data-item>
                        <input type="checkbox" data-key="task-{{ $task->id }}" aria-label="Done: {{ $task->task_name }}">
                        <div>
                            <span class="sp-what" style="display:block">{{ $task->task_name }}</span>
                            <span class="sp-how" style="display:block">{{ $task->subject?->subject_name ?? 'No subject' }} &middot; {{ $task->due_label }}</span>
                        </div>
                        <form method="POST" action="{{ route('tasks.complete', $task) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="pg-act ok">Mark complete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

    @endif

    <p style="margin-top:22px;font-size:13px;color:#8d92ad">
        Short on energy today?
        <a href="{{ route('study_plan.today', ['mode' => 'lite']) }}" style="color:#7db4ff">Switch to the low-energy day.</a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('.pg[data-day]');
    if (!root) return;

    var day = root.dataset.day;
    var boxes = Array.prototype.slice.call(root.querySelectorAll('input[data-key]'));
    var done = root.querySelector('[data-done]');
    var total = root.querySelector('[data-total]');
    var pct = root.querySelector('[data-pct]');
    var bar = root.querySelector('[data-bar]');

    function storeKey(box) { return 'sp:' + day + ':' + box.dataset.key; }

    function refresh() {
        var checked = boxes.filter(function (b) { return b.checked; }).length;
        var share = boxes.length ? Math.round(checked / boxes.length * 100) : 0;

        if (done) done.textContent = checked;
        if (total) total.textContent = boxes.length;
        if (pct) pct.textContent = share + '%';
        if (bar) bar.style.width = share + '%';
    }

    boxes.forEach(function (box) {
        try { box.checked = localStorage.getItem(storeKey(box)) === '1'; } catch (e) {}

        var row = box.closest('[data-item]');
        if (row) row.classList.toggle('done', box.checked);

        box.addEventListener('change', function () {
            try { localStorage.setItem(storeKey(box), box.checked ? '1' : '0'); } catch (e) {}
            if (row) row.classList.toggle('done', box.checked);
            refresh();
        });
    });

    refresh();
});
</script>
@endsection
