@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-7">
        <h2 class="page-title">My Tasks</h2>
        <p class="page-sub">Create, manage, and track your study tasks.</p>
    </div>

    {{-- Add Task --}}
    <div class="glass p-7 mb-6">
        <h3 class="text-xl font-bold text-white">Add New Task</h3>
        <p class="text-sm mt-1 mb-6" style="color: var(--muted)">Add a task and organize it by subject, deadline, and priority.</p>

        @if ($errors->any())
            <div class="alert alert-err" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($subjects->count() > 0)

            <form method="POST" action="{{ route('tasks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @csrf

                {{-- Task Name --}}
                <div class="sm:col-span-2">
                    <label for="task_name" class="f-label">Task Name</label>
                    <div class="control">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                               placeholder="Enter task name" maxlength="150" required
                               class="f-input has-icon">
                    </div>
                </div>

                {{-- Subject --}}
                <div>
                    <label for="subject_id" class="f-label">Subject</label>
                    <div class="control">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <select id="subject_id" name="subject_id" required class="f-input has-icon">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Deadline --}}
                <div>
                    <label for="deadline" class="f-label">Deadline</label>
                    <div class="control">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" required class="f-input has-icon">
                    </div>
                </div>

                {{-- Priority --}}
                <div>
                    <label for="priority" class="f-label">Priority</label>
                    <div class="control">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        <select id="priority" name="priority" required class="f-input has-icon">
                            <option value="High" @selected(old('priority') === 'High')>High</option>
                            <option value="Medium" @selected(old('priority', 'Medium') === 'Medium')>Medium</option>
                            <option value="Low" @selected(old('priority') === 'Low')>Low</option>
                        </select>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">Add Task</button>
                </div>

            </form>

        @else

            <div class="text-center py-10">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-sm mt-1" style="color: var(--muted)">You need to create a subject before adding a task.</p>
                <div class="mt-5">
                    <a href="{{ route('subjects.index') }}" class="btn-primary" style="height:44px">Add Subject</a>
                </div>
            </div>

        @endif
    </div>

    {{-- Task List --}}
    <div class="glass p-7">

        <div class="mb-6">
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

            <div id="task-list" class="flex flex-col gap-4">

                @foreach ($tasks as $task)

                    @php
                        $prioClass = ['High' => 'chip-high', 'Medium' => 'chip-med', 'Low' => 'chip-low'][$task->priority] ?? 'chip-muted';
                    @endphp

                    <div id="task-{{ $task->id }}" data-search-item data-name="{{ mb_strtolower($task->task_name) }}" data-filter="{{ $task->subject_id }}"
                         class="rounded-2xl p-5 border border-white/[.08] bg-white/[.03] hover:border-blue-500/30 transition">
                        <div class="flex flex-wrap items-start justify-between gap-5">

                            {{-- Task Information --}}
                            <div class="flex-1 min-w-[240px]">
                                <h4 data-search-name class="text-white font-semibold text-lg mb-3">{{ $task->task_name }}</h4>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="chip chip-blue">{{ $task->subject->subject_name }}</span>

                                    <span class="chip chip-muted">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ $task->deadline->format('F d, Y') }}
                                    </span>

                                    <span class="chip {{ $prioClass }}">{{ $task->priority }}</span>

                                    @if ($task->status === 'Completed')
                                        <span class="chip chip-low">Completed</span>
                                    @else
                                        <span class="chip chip-warn">Pending</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center gap-2">

                                @if ($task->status === 'Completed')
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

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No tasks yet</h4>
                <p class="text-sm mt-1" style="color: var(--muted)">Add your first task above to start organizing your study work.</p>
            </div>

        @endif

    </div>

</div>

@endsection