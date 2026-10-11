@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')

@php
    $pendingCount   = $tasks->where('status', '!=', 'Completed')->count();
    $completedCount = $tasks->where('status', 'Completed')->count();
    $overdueCount   = $tasks->filter(fn ($t) => $t->is_overdue)->count();

    $accent = [
        'High'   => 'border-l-rose-500',
        'Medium' => 'border-l-yellow-500',
        'Low'    => 'border-l-green-500',
    ];

    $icTask = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>';
    $icBook = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
    $icCal  = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    $icFlag = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>';
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="page-title">My Tasks</h2>
            <p class="page-sub">Create, manage, and track your study tasks.</p>
        </div>

        @if ($subjects->count() > 0)
            <button type="button" data-open-add
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Task
            </button>
        @else
            <a href="{{ route('subjects.index') }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">
                Add Subject first
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-ok" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err" role="alert">{{ $errors->first() }}</div>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $pendingCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Pending</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $completedCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Completed</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-rose-500/15 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $overdueCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Overdue</p>
            </div>
        </div>

    </div>

    {{-- Task List --}}
    <div class="mb-5">
        <div class="flex items-center gap-3">
            <h3 class="text-xl font-bold text-white">All Tasks</h3>
            <span class="chip chip-blue">{{ $tasks->count() }} {{ $tasks->count() == 1 ? 'task' : 'tasks' }}</span>
        </div>
        <p class="text-sm mt-1" style="color: var(--muted)">Everything in your planner, soonest deadline first.</p>

        {{-- Search bar (shared component): type a letter, filter by subject, Clear --}}
        @if ($tasks->count() > 0)
            <div class="mt-5">
                @include('partials.list-search', [
                    'placeholder' => 'Type a letter to find a task...',
                    'filterAll'   => 'All subjects',
                    'filters'     => $subjects->pluck('subject_name', 'id')->all(),
                    'target'      => '#task-list',
                    'emptyText'   => 'No task matches your search.',
                ])
            </div>
        @endif
    </div>

    @if ($tasks->count() > 0)

        <div id="task-list" class="flex flex-col gap-3">

            @foreach ($tasks as $task)

                @php
                    $prioClass = ['High' => 'chip-high', 'Medium' => 'chip-med', 'Low' => 'chip-low'][$task->priority] ?? 'chip-muted';
                    $isDone    = $task->status === 'Completed';
                @endphp

                <div id="task-{{ $task->id }}" data-search-item data-name="{{ mb_strtolower($task->task_name) }}" data-filter="{{ $task->subject_id }}"
                     class="rounded-2xl p-5 border border-white/[.08] border-l-4 {{ $isDone ? 'border-l-gray-600' : ($accent[$task->priority] ?? 'border-l-gray-500') }} bg-white/[.03] hover:border-blue-500/30 transition">
                    <div class="flex flex-wrap items-center justify-between gap-4">

                        {{-- Task Information --}}
                        <div class="flex-1 min-w-[240px]">
                            <h4 data-search-name class="font-semibold text-lg mb-3 {{ $isDone ? 'text-gray-500 line-through' : 'text-white' }}">{{ $task->task_name }}</h4>

                            <div class="flex flex-wrap items-center gap-2">
                                <span class="chip chip-blue">{{ $task->subject->subject_name }}</span>

                                <span class="chip chip-muted">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ $task->deadline->format('F d, Y') }}
                                </span>

                                <span class="chip {{ $prioClass }}">{{ $task->priority }}</span>

                                @if ($isDone)
                                    <span class="chip chip-low">Completed</span>
                                @else
                                    <span class="chip {{ $task->is_overdue ? 'chip-high' : 'chip-warn' }}">{{ $task->is_overdue ? $task->due_label : 'Pending' }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2">

                            <button type="button" class="btn-sm edit-btn"
                                    style="background:rgba(255,255,255,.04);border:1px solid #2a2a38;color:#cfd2e0"
                                    data-task="{{ json_encode([
                                        'id'         => $task->id,
                                        'name'       => $task->task_name,
                                        'subject_id' => $task->subject_id,
                                        'deadline'   => $task->deadline->format('Y-m-d'),
                                        'priority'   => $task->priority,
                                    ]) }}">
                                Edit
                            </button>

                            @if ($isDone)
                                <form method="POST" action="{{ route('tasks.update', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Pending">
                                    <button type="submit" class="btn-sm btn-warn">Mark Pending</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('tasks.update', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Completed">
                                    <button type="submit" class="btn-sm btn-ok">Complete</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                            </form>

                        </div>

                    </div>
                </div>

            @endforeach

        </div>

    @else

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl text-center py-16 px-6">
            <span class="w-14 h-14 mx-auto rounded-2xl bg-blue-600/15 border border-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </span>

            @if ($subjects->count() > 0)
                <h4 class="font-semibold text-white">No tasks yet</h4>
                <p class="text-sm mt-1 mb-5" style="color: var(--muted)">Add your first task to start organizing your study work.</p>
                <button type="button" data-open-add class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">+ Add Task</button>
            @else
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-sm mt-1 mb-5" style="color: var(--muted)">You need to create a subject before adding a task.</p>
                <a href="{{ route('subjects.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">Add Subject</a>
            @endif
        </div>

    @endif

</div>

@if ($subjects->count() > 0)

{{-- Add dialog --}}
<dialog id="add-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" action="{{ route('tasks.store') }}" class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-200">
        @csrf

        <div class="sm:col-span-2">
            <h3 class="text-lg font-bold text-white">Add new task</h3>
            <p class="text-sm mt-1" style="color: var(--muted)">Organize it by subject, deadline, and priority.</p>
        </div>

        <div class="sm:col-span-2">
            <label for="task_name" class="f-label">Task Name</label>
            <div class="control">
                {!! $icTask !!}
                <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                       placeholder="Enter task name" maxlength="150" required class="f-input has-icon">
            </div>
        </div>

        <div>
            <label for="subject_id" class="f-label">Subject</label>
            <div class="control">
                {!! $icBook !!}
                <select id="subject_id" name="subject_id" required class="f-input has-icon">
                    <option value="">Select Subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="deadline" class="f-label">Deadline</label>
            <div class="control">
                {!! $icCal !!}
                <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" required class="f-input has-icon">
            </div>
        </div>

        <div class="sm:col-span-2">
            <label for="priority" class="f-label">Priority</label>
            <div class="control">
                {!! $icFlag !!}
                <select id="priority" name="priority" required class="f-input has-icon">
                    <option value="High" @selected(old('priority') === 'High')>High</option>
                    <option value="Medium" @selected(old('priority', 'Medium') === 'Medium')>Medium</option>
                    <option value="Low" @selected(old('priority') === 'Low')>Low</option>
                </select>
            </div>
        </div>

        <div class="sm:col-span-2 flex justify-end gap-2 pt-1">
            <button type="button" id="add-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">Add Task</button>
        </div>
    </form>
</dialog>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-200">
        @csrf
        @method('PATCH')

        <div class="sm:col-span-2">
            <h3 class="text-lg font-bold text-white">Edit task</h3>
        </div>

        <div class="sm:col-span-2">
            <label for="e_name" class="f-label">Task Name</label>
            <div class="control">
                {!! $icTask !!}
                <input id="e_name" type="text" name="task_name" maxlength="150" required class="f-input has-icon">
            </div>
        </div>

        <div>
            <label for="e_subject" class="f-label">Subject</label>
            <div class="control">
                {!! $icBook !!}
                <select id="e_subject" name="subject_id" required class="f-input has-icon">
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="e_deadline" class="f-label">Deadline</label>
            <div class="control">
                {!! $icCal !!}
                <input id="e_deadline" type="date" name="deadline" required class="f-input has-icon">
            </div>
        </div>

        <div class="sm:col-span-2">
            <label for="e_priority" class="f-label">Priority</label>
            <div class="control">
                {!! $icFlag !!}
                <select id="e_priority" name="priority" required class="f-input has-icon">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>
        </div>

        <div class="sm:col-span-2 flex justify-end gap-2 pt-1">
            <button type="button" id="edit-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">Save changes</button>
        </div>
    </form>
</dialog>

<script>
(() => {
    const add  = document.getElementById('add-dialog');
    const dlg  = document.getElementById('edit-dialog');
    const form = document.getElementById('edit-form');

    // Add dialog
    document.querySelectorAll('[data-open-add]').forEach(b => b.addEventListener('click', () => {
        add.showModal();
        document.getElementById('task_name').focus();
    }));
    document.getElementById('add-cancel').addEventListener('click', () => add.close());
    add.addEventListener('click', e => { if (e.target === add) add.close(); });

    // Edit dialog
    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => {
        const t = JSON.parse(b.dataset.task);
        form.action = "{{ url('/tasks') }}/" + t.id;
        document.getElementById('e_name').value     = t.name;
        document.getElementById('e_subject').value  = t.subject_id;
        document.getElementById('e_deadline').value = t.deadline;
        document.getElementById('e_priority').value = t.priority;
        dlg.showModal();
    }));

    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
})();
</script>

@endif

@endsection