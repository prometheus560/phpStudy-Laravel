@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
@php
    $pendingCount = $tasks->where('status', '!=', 'Completed')->count();
    $completedCount = $tasks->where('status', 'Completed')->count();
    $overdueCount = $tasks->filter(fn ($task) => $task->is_overdue)->count();
    $barColor = ['High' => '#fb7185', 'Medium' => '#fbbf24', 'Low' => '#60a5fa'];
@endphp

@include('partials.page-kit')

<div class="pg">

    {{-- Page Header --}}
    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">My Tasks</h1>
            <p class="pg-sub">Create, manage, and track your study tasks.</p>
        </div>

        <div class="pg-actions">
            <form method="POST" action="{{ route('tasks.destroy_all') }}"
                  onsubmit="return confirm('Are you sure you want to delete all tasks? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" @disabled($tasks->isEmpty())
                        title="{{ $tasks->isEmpty() ? 'No tasks to delete.' : 'Delete all your tasks' }}"
                        class="pg-btn danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    Delete All
                </button>
            </form>

            @if ($subjects->isNotEmpty())
                <a href="{{ route('tasks.create') }}" class="pg-btn main">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Task
                </a>
            @else
                <a href="{{ route('subjects.index') }}" class="pg-btn main">Add Subject First</a>
            @endif
        </div>
    </header>

    {{-- Flash Messages --}}
    @if (session('success'))<div class="pg-flash" role="status">{{ session('success') }}</div>@endif
    @if (session('info'))<div class="pg-flash info" role="status">{{ session('info') }}</div>@endif
    @if ($errors->any())<div class="pg-flash err" role="alert">{{ $errors->first() }}</div>@endif

    {{-- Readout strip --}}
    <div class="pg-stats">
        <div class="pg-stat" style="--c:#fb923c"><b>{{ $pendingCount }}</b><span>Pending</span></div>
        <div class="pg-stat" style="--c:#4ade80"><b>{{ $completedCount }}</b><span>Completed</span></div>
        <div class="pg-stat" style="--c:#fb7185"><b>{{ $overdueCount }}</b><span>Overdue</span></div>
    </div>

    <div class="pg-section">
        <h3>All Tasks</h3>
        <small>{{ $tasks->count() }} {{ $tasks->count() === 1 ? 'task' : 'tasks' }}, ordered by deadline</small>
    </div>

    @if ($tasks->isNotEmpty())

        {{-- Search and Subject Filter --}}
        <div class="pg-tools">
            <input type="search" id="task-search" placeholder="Search tasks..." aria-label="Search tasks" class="pg-input">

            <select id="subject-filter" aria-label="Filter by subject" class="pg-input">
                <option value="">All subjects</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Bulk Delete Form --}}
        <form id="bulk-delete-form" method="POST" action="{{ route('tasks.bulk_destroy') }}"
              onsubmit="return confirm('Are you sure you want to delete the selected tasks? This action cannot be undone.');">
            @csrf
            @method('DELETE')

            <div class="pg-bulk">
                <label>
                    <input type="checkbox" id="select-all" class="pg-check" style="width:16px;height:16px;accent-color:#6366f1">
                    Select all visible tasks
                </label>

                <button type="submit" id="delete-selected" disabled class="pg-btn danger" style="height:36px;font-size:12.5px">
                    Delete Selected (<span id="selected-count">0</span>)
                </button>
            </div>
        </form>

        {{-- Task Cards --}}
        <div id="task-list" class="pg-tlist">

            @foreach ($tasks as $task)
                @php
                    $isCompleted = $task->status === 'Completed';
                    $subjectName = $task->subject?->subject_name ?? 'No subject';
                    $dl = $task->deadline ? \Illuminate\Support\Carbon::parse($task->deadline)->startOfDay() : null;
                    $late = !$isCompleted && $task->is_overdue;
                    $state = $isCompleted ? 'done' : ($late ? 'late' : ($dl && $dl->isToday() ? 'now' : ''));
                    $prio = strtolower($task->priority ?? '');
                @endphp

                <article class="task-card pg-task"
                         style="--bar: {{ $barColor[$task->priority] ?? '#5b5f78' }}"
                         data-task-name="{{ strtolower($task->task_name) }}"
                         data-subject-id="{{ $task->subject_id }}">

                    <input type="checkbox" name="ids[]" value="{{ $task->id }}" form="bulk-delete-form"
                           class="task-checkbox pg-check" aria-label="Select {{ $task->task_name }}">

                    @if ($isCompleted)
                        <div class="pg-date done"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></div>
                    @elseif ($dl)
                        <div class="pg-date {{ $state }}"><small>{{ $dl->format('M') }}</small><b>{{ $dl->format('d') }}</b></div>
                    @else
                        <div class="pg-date"><small>No</small><b>&ndash;</b></div>
                    @endif

                    <div style="min-width:0">
                        <h4 class="pg-name">{{ $task->task_name }}</h4>
                        <div class="pg-meta">
                            <span class="pg-pill">{{ $subjectName }}</span>
                            <span class="pg-pill pg-p-{{ $prio }}">{{ $task->priority }} Priority</span>
                            <span class="pg-pill {{ $isCompleted ? 'pg-p-low' : '' }}" @if($isCompleted) style="color:#86efac;border-color:rgba(74,222,128,.4);background:rgba(74,222,128,.1)" @endif>{{ $task->status }}</span>
                            @if ($late)<span class="pg-pill pg-p-high">Overdue</span>@endif
                            <span>Deadline {{ $dl ? $dl->format('M d, Y') : 'not set' }}</span>
                        </div>
                    </div>

                    {{-- Task Actions --}}
                    <div class="pg-task-acts">
                        <a href="{{ route('tasks.edit', $task) }}" class="pg-act edit">Edit</a>

                        @if (!$isCompleted)
                            <form method="POST" action="{{ route('tasks.complete', $task) }}"
                                  onsubmit="return confirm('Mark this task as completed?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="pg-act ok">Complete</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                              onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pg-act del">Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach

        </div>

        <p id="no-search-results" class="hidden pg-empty" style="padding:30px 0"><span style="color:#8d92ad;font-size:13.5px">No tasks match your search.</span></p>

    @else

        {{-- Empty State --}}
        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <b>No tasks yet</b>
            <p>Your tasks will appear here after you create one.</p>
            @if ($subjects->isNotEmpty())
                <a href="{{ route('tasks.create') }}" class="pg-cta">Add Your First Task</a>
            @else
                <a href="{{ route('subjects.index') }}" class="pg-cta">Create a Subject First</a>
            @endif
        </div>

    @endif

</div>

{{-- Search, Filter, and Selection --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('task-search');
    const subjectFilter = document.getElementById('subject-filter');
    const selectAll = document.getElementById('select-all');
    const deleteSelected = document.getElementById('delete-selected');
    const selectedCount = document.getElementById('selected-count');
    const noResults = document.getElementById('no-search-results');

    const cards = Array.from(document.querySelectorAll('.task-card'));
    const checkboxes = Array.from(document.querySelectorAll('.task-checkbox'));

    function visibleCards() {
        return cards.filter(card => !card.classList.contains('hidden'));
    }

    function updateSelection() {
        const selected = checkboxes.filter(box => box.checked);
        const visible = visibleCards();
        const visibleCheckboxes = visible
            .map(card => card.querySelector('.task-checkbox'))
            .filter(Boolean);

        selectedCount.textContent = selected.length;
        deleteSelected.disabled = selected.length === 0;

        if (selectAll) {
            selectAll.checked =
                visibleCheckboxes.length > 0 &&
                visibleCheckboxes.every(box => box.checked);

            selectAll.indeterminate =
                visibleCheckboxes.some(box => box.checked) &&
                !visibleCheckboxes.every(box => box.checked);
        }
    }

    function filterTasks() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const subjectId = subjectFilter ? subjectFilter.value : '';

        cards.forEach(card => {
            const name = card.dataset.taskName || '';
            const cardSubject = card.dataset.subjectId || '';

            const matchesName = name.includes(query);
            const matchesSubject = !subjectId || cardSubject === subjectId;

            card.classList.toggle('hidden', !(matchesName && matchesSubject));
        });

        const hasVisibleCards = visibleCards().length > 0;

        if (noResults) {
            noResults.classList.toggle('hidden', hasVisibleCards);
        }

        updateSelection();
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTasks);
    }

    if (subjectFilter) {
        subjectFilter.addEventListener('change', filterTasks);
    }

    checkboxes.forEach(box => {
        box.addEventListener('change', updateSelection);
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            visibleCards().forEach(card => {
                const checkbox = card.querySelector('.task-checkbox');

                if (checkbox) {
                    checkbox.checked = selectAll.checked;
                }
            });

            updateSelection();
        });
    }

    updateSelection();
});
</script>
@endsection