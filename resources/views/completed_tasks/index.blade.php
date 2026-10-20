@extends('layouts.app')

@section('title', 'Completed Tasks')

@section('content')
    @php
        $list = collect($tasks)->values();
        $total = $list->count();

        $subjectFilter = $list
            ->map(fn ($task) => $task->subject)
            ->filter()
            ->unique('id')
            ->sortBy('subject_name')
            ->pluck('subject_name', 'id');
    @endphp

    @include('partials.page-kit')

    <div class="pg">

        <header class="pg-head">
            <div style="position:relative;z-index:1">
                <h1 class="pg-title">Completed Tasks</h1>
                <p class="pg-sub">{{ $total }} {{ $total === 1 ? 'completed task' : 'completed tasks' }}</p>
            </div>
            <a href="{{ route('tasks.index') }}" class="pg-back">&larr; Back to Tasks</a>
        </header>

        @if (session('success'))
            <div class="pg-flash" role="status">{{ session('success') }}</div>
        @endif

        @if ($total > 0)

            <div class="pg-tools">
                <input id="completed-search" type="search" placeholder="Search completed tasks..." aria-label="Search completed tasks" class="pg-input">

                <select id="completed-subject" aria-label="Filter by subject" class="pg-input">
                    <option value="">All subjects</option>
                    @foreach ($subjectFilter as $subjectId => $subjectName)
                        <option value="{{ $subjectId }}">{{ $subjectName }}</option>
                    @endforeach
                </select>
            </div>

            <p id="completed-count" class="pg-count" aria-live="polite">
                {{ $total }} {{ $total === 1 ? 'task' : 'tasks' }}
            </p>

            <div id="completed-list" class="pg-panel">
                @foreach ($list as $task)
                    @php
                        $priority = strtolower($task->priority ?? '');
                        $subjectName = $task->subject?->subject_name ?? 'No subject';
                    @endphp

                    <article
                        data-completed-card
                        data-name="{{ mb_strtolower($task->task_name ?? '') }}"
                        data-subject="{{ $task->subject_id ?? '' }}"
                        class="pg-row"
                    >
                        <span class="pg-date done">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>
                        </span>

                        <div style="min-width:0">
                            <h2 class="pg-name">{{ $task->task_name }}</h2>
                            <div class="pg-meta">
                                <span>{{ $subjectName }}</span>
                                @if ($priority !== '')
                                    <span class="pg-pill pg-p-{{ $priority }}">{{ ucfirst($priority) }}</span>
                                @endif
                                @if ($task->deadline)
                                    <span>Deadline {{ \Illuminate\Support\Carbon::parse($task->deadline)->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div id="completed-no-results" hidden class="pg-panel pg-empty" style="margin-top:12px">
                <p>No matching tasks.</p>
            </div>

        @else

            <div class="pg-panel pg-empty">
                <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
                <b>No completed tasks yet</b>
                <p>Tasks you complete will appear here.</p>
                <a href="{{ route('tasks.index') }}" class="pg-cta">View Tasks</a>
            </div>

        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('completed-search');
            const subject = document.getElementById('completed-subject');
            const count = document.getElementById('completed-count');
            const empty = document.getElementById('completed-no-results');

            if (!search || !subject || !count || !empty) {
                return;
            }

            const cards = Array.from(
                document.querySelectorAll('[data-completed-card]')
            );

            function filterCompletedTasks() {
                const term = search.value.trim().toLowerCase();
                const selectedSubject = subject.value;
                let visibleCount = 0;

                cards.forEach(card => {
                    const matchesName =
                        (card.dataset.name || '').includes(term);

                    const matchesSubject =
                        selectedSubject === '' ||
                        card.dataset.subject === selectedSubject;

                    const visible = matchesName && matchesSubject;
                    card.hidden = !visible;

                    if (visible) {
                        visibleCount++;
                    }
                });

                count.textContent =
                    `${visibleCount} of ${cards.length} ${cards.length === 1 ? 'task' : 'tasks'}`;

                empty.hidden = visibleCount !== 0;
            }

            search.addEventListener('input', filterCompletedTasks);
            subject.addEventListener('change', filterCompletedTasks);
        });
    </script>
@endsection