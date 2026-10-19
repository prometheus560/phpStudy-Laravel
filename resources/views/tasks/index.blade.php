@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
@php
    $pendingCount = $tasks->where('status', '!=', 'Completed')->count();
    $completedCount = $tasks->where('status', 'Completed')->count();
    $overdueCount = $tasks->filter(fn ($task) => $task->is_overdue)->count();
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="page-title">My Tasks</h2>
            <p class="page-sub">Create, manage, and track your study tasks.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Delete All Tasks --}}
            <form
                method="POST"
                action="{{ route('tasks.destroy_all') }}"
                onsubmit="return confirm('Are you sure you want to delete all tasks? This action cannot be undone.');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    @disabled($tasks->isEmpty())
                    title="{{ $tasks->isEmpty() ? 'No tasks to delete.' : 'Delete all your tasks' }}"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold border border-red-500/40 text-red-300 bg-red-500/10 hover:bg-red-500/20 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                    Delete All
                </button>
            </form>

            @if ($subjects->isNotEmpty())
                <a
                    href="{{ route('tasks.create') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500 transition"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Task
                </a>
            @else
                <a
                    href="{{ route('subjects.index') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold"
                >
                    Add Subject First
                </a>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-ok mb-5" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert mb-5" role="status">
            {{ session('info') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err mb-5" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $pendingCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Pending</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $completedCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Completed</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $overdueCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Overdue</p>
            </div>
        </div>

    </div>

    {{-- Task List Heading --}}
    <div class="mb-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h3 class="text-xl font-bold text-white">All Tasks</h3>
                <span class="chip chip-blue">
                    {{ $tasks->count() }}
                    {{ $tasks->count() === 1 ? 'task' : 'tasks' }}
                </span>
            </div>
        </div>
        <p class="text-sm mt-1" style="color: var(--muted)">
            Everything in your planner, ordered by deadline.
        </p>
    </div>

    @if ($tasks->isNotEmpty())

        {{-- Search and Subject Filter --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            <input
                type="search"
                id="task-search"
                placeholder="Search tasks..."
                aria-label="Search tasks"
                class="w-full rounded-xl border border-[#303040] bg-[#12121c] text-white px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
            >

            <select
                id="subject-filter"
                aria-label="Filter by subject"
                class="w-full rounded-xl border border-[#303040] bg-[#12121c] text-white px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
            >
                <option value="">All subjects</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">
                        {{ $subject->subject_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Bulk Delete Form --}}
        <form
            id="bulk-delete-form"
            method="POST"
            action="{{ route('tasks.bulk_destroy') }}"
            onsubmit="return confirm('Are you sure you want to delete the selected tasks? This action cannot be undone.');"
        >
            @csrf
            @method('DELETE')

            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input
                        type="checkbox"
                        id="select-all"
                        class="w-4 h-4 accent-blue-500"
                    >
                    Select all visible tasks
                </label>

                <button
                    type="submit"
                    id="delete-selected"
                    disabled
                    class="rounded-xl px-4 py-2 text-sm font-semibold border border-red-500/30 text-red-300 bg-red-500/10 hover:bg-red-500/20 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    Delete Selected
                    (<span id="selected-count">0</span>)
                </button>
            </div>
        </form>

        {{-- Task Cards --}}
        <div id="task-list" class="space-y-3">

            @foreach ($tasks as $task)
                @php
                    $priorityClasses = [
                        'High' => 'border-l-rose-500',
                        'Medium' => 'border-l-yellow-500',
                        'Low' => 'border-l-green-500',
                    ];

                    $priorityBadge = [
                        'High' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
                        'Medium' => 'bg-yellow-500/10 text-yellow-300 border-yellow-500/20',
                        'Low' => 'bg-green-500/10 text-green-300 border-green-500/20',
                    ];

                    $isCompleted = $task->status === 'Completed';
                    $subjectName = $task->subject?->subject_name ?? 'No subject';
                @endphp

                <article
                    class="task-card bg-[#14141f] border border-[#23232f] border-l-4 {{ $priorityClasses[$task->priority] ?? 'border-l-gray-500' }} rounded-2xl p-5"
                    data-task-name="{{ strtolower($task->task_name) }}"
                    data-subject-id="{{ $task->subject_id }}"
                >
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                        <div class="flex items-start gap-3 min-w-0">
                            <input
                                type="checkbox"
                                name="ids[]"
                                value="{{ $task->id }}"
                                form="bulk-delete-form"
                                class="task-checkbox mt-1 w-4 h-4 accent-blue-500 shrink-0"
                                aria-label="Select {{ $task->task_name }}"
                            >

                            <div class="min-w-0">
                                <h4 class="text-base font-semibold text-white break-words">
                                    {{ $task->task_name }}
                                </h4>

                                <p class="text-sm text-gray-400 mt-1">
                                    {{ $subjectName }}
                                </p>

                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-medium {{ $priorityBadge[$task->priority] ?? 'bg-gray-500/10 text-gray-300 border-gray-500/20' }}">
                                        {{ $task->priority }} Priority
                                    </span>

                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-medium {{ $isCompleted ? 'bg-green-500/10 text-green-300 border-green-500/20' : 'bg-blue-500/10 text-blue-300 border-blue-500/20' }}">
                                        {{ $task->status }}
                                    </span>

                                    @if (!$isCompleted && $task->is_overdue)
                                        <span class="inline-flex items-center rounded-lg border border-rose-500/20 bg-rose-500/10 text-rose-300 px-2.5 py-1 text-xs font-medium">
                                            Overdue
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-400 mt-3">
                                    Deadline:
                                    <span class="text-gray-200">
                                        {{ $task->deadline ? \Illuminate\Support\Carbon::parse($task->deadline)->format('M d, Y') : 'No deadline' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Task Actions --}}
                        <div class="flex flex-wrap items-center gap-2 lg:justify-end lg:shrink-0">

                            <a
                                href="{{ route('tasks.edit', $task) }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-blue-300 border border-blue-500/20 bg-blue-500/10 hover:bg-blue-500/20 transition"
                            >
                                Edit
                            </a>

                            @if (!$isCompleted)
                                <form
                                    method="POST"
                                    action="{{ route('tasks.complete', $task) }}"
                                    onsubmit="return confirm('Mark this task as completed?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded-lg px-3 py-2 text-sm font-medium text-green-300 border border-green-500/20 bg-green-500/10 hover:bg-green-500/20 transition"
                                    >
                                        Complete
                                    </button>
                                </form>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('tasks.destroy', $task) }}"
                                onsubmit="return confirm('Are you sure you want to delete this task?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded-lg px-3 py-2 text-sm font-medium text-red-300 border border-red-500/20 bg-red-500/10 hover:bg-red-500/20 transition"
                                >
                                    Delete
                                </button>
                            </form>

                        </div>
                    </div>
                </article>
            @endforeach

        </div>

        <p id="no-search-results" class="hidden text-center text-sm text-gray-400 py-10">
            No tasks match your search.
        </p>

    @else

        {{-- Empty State --}}
        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl px-6 py-14 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-400" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>

            <h3 class="text-lg font-semibold text-white">No tasks yet</h3>
            <p class="text-sm text-gray-400 mt-2">
                Your tasks will appear here after you create one.
            </p>

            @if ($subjects->isNotEmpty())
                <a
                    href="{{ route('tasks.create') }}"
                    class="inline-flex items-center gap-2 mt-5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                >
                    Add Your First Task
                </a>
            @else
                <a
                    href="{{ route('subjects.index') }}"
                    class="inline-flex items-center gap-2 mt-5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                >
                    Create a Subject First
                </a>
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
