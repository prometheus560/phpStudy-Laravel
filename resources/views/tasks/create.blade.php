@extends('layouts.app')

@section('title', 'Add Task')

@section('content')

<div class="max-w-2xl mx-auto px-6 py-8">

    <a href="{{ route('tasks.index') }}" class="text-sm hover:text-white" style="color: var(--muted)">&larr; Back to Tasks</a>

    <div class="mt-4 mb-7">
        <h2 class="page-title">Add Task</h2>
        <p class="page-sub">Add a task and organize it by subject, deadline, and priority.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-err" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="glass p-7">

        @if ($subjects->isNotEmpty())

            <form method="POST" action="{{ route('tasks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @csrf

                {{-- Task Name --}}
                <div class="sm:col-span-2">
                    <label for="task_name" class="f-label">Task Name</label>
                    <div class="control">
                        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                               placeholder="Enter task name" maxlength="255" required autofocus
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
                                <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>
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
                <div class="sm:col-span-2">
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

                {{-- Buttons --}}
                <div class="sm:col-span-2 flex flex-wrap items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Add Task</button>
                    <a href="{{ route('tasks.index') }}" class="btn-ghost" style="height: 48px;">Cancel</a>
                </div>

            </form>

        @else

            <div class="text-center py-10">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-sm mt-1" style="color: var(--muted)">You need to create a subject before adding a task.</p>
                <div class="mt-5">
                    <a href="{{ route('subjects.index') }}" class="btn-primary" style="height: 44px;">Add Subject</a>
                </div>
            </div>

        @endif

    </div>

</div>

@endsection