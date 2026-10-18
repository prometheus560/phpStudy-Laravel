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
            ->pluck('subject_name', 'id')
            ->all();

        $priorityStyles = [
            'high' => 'border-red-500/20 bg-red-500/10 text-red-300',
            'medium' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',
            'low' => 'border-blue-500/20 bg-blue-500/10 text-blue-300',
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Completed Tasks
                </h1>

                @if ($total > 0)
                    <p class="mt-2 text-sm text-gray-400">
                        {{ $total }} completed
                    </p>
                @endif
            </div>

            <a
                href="{{ route('tasks.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-300 transition hover:bg-white/5 hover:text-white"
            >
                &larr; Back to Tasks
            </a>
        </header>

        @if ($total > 0)

            {{-- Search --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                <input
                    id="completed-search"
                    type="search"
                    placeholder="Search completed tasks..."
                    aria-label="Search completed tasks"
                    class="w-full rounded-xl border border-gray-700 bg-gray-900 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none"
                >

                <select
                    id="completed-subject"
                    aria-label="Filter by subject"
                    class="w-full rounded-xl border border-gray-700 bg-gray-900 px-4 py-3 text-sm text-white focus:border-blue-500 focus:outline-none"
                >
                    <option value="">All subjects</option>

                    @foreach ($subjectFilter as $subjectId => $subjectName)
                        <option value="{{ $subjectId }}">
                            {{ $subjectName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <p
                id="completed-count"
                class="mb-4 text-sm text-gray-500"
                aria-live="polite"
            >
                {{ $total }} {{ $total === 1 ? 'task' : 'tasks' }}
            </p>

            {{-- Task list: retains the controller's ordering --}}
            <div id="completed-list" class="flex flex-col gap-3">
                @foreach ($list as $task)
                    @php
                        $priority = strtolower($task->priority ?? '');

                        $priorityClass = $priorityStyles[$priority]
                            ?? 'border-gray-700 bg-gray-800 text-gray-300';
                    @endphp

                    <article
                        data-completed-card
                        data-name="{{ mb_strtolower($task->task_name) }}"
                        data-subject="{{ $task->subject_id }}"
                        class="rounded-2xl border border-gray-800 bg-gray-900/50 p-4 sm:p-5 transition hover:border-gray-700"
                    >
                        <div class="flex items-start gap-4">

                            {{-- Completed icon --}}
                            <span
                                class="flex shrink-0 items-center justify-center rounded-xl"
                                style="
                                    width: 40px;
                                    height: 40px;
                                    color: #4ade80;
                                    background: rgba(34, 197, 94, 0.10);
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    style="width: 20px; height: 20px; flex-shrink: 0;"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                            </span>

                            {{-- Task details --}}
                            <div class="min-w-0 flex-1">
                                <h2 class="break-words text-base font-semibold leading-6 text-white">
                                    {{ $task->task_name }}
                                </h2>

                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2">
                                    @if ($task->subject)
                                        <span class="text-sm text-gray-400">
                                            {{ $task->subject->subject_name }}
                                        </span>
                                    @endif

                                    @if ($priority !== '')
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $priorityClass }}">
                                            {{ ucfirst($priority) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- No search matches --}}
            <div
                id="completed-no-results"
                hidden
                class="rounded-2xl border border-gray-800 px-6 py-12 text-center"
            >
                <p class="text-sm text-gray-400">
                    No matching tasks.
                </p>
            </div>

        @else

            {{-- Empty state --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900/40 px-6 py-14 text-center">
                <span
                    class="mx-auto flex items-center justify-center rounded-2xl"
                    style="
                        width: 56px;
                        height: 56px;
                        color: #4ade80;
                        background: rgba(34, 197, 94, 0.10);
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="28"
                        height="28"
                        style="width: 28px; height: 28px; flex-shrink: 0;"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </span>

                <h2 class="mt-5 text-lg font-semibold text-white">
                    No completed tasks yet
                </h2>

                <a
                    href="{{ route('tasks.index') }}"
                    class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"
                >
                    View Tasks
                </a>
            </div>

        @endif

    </div>

    <script>
        (() => {
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
                    const matchesName = card.dataset.name.includes(term);

                    const matchesSubject = selectedSubject === ''
                        || card.dataset.subject === selectedSubject;

                    const visible = matchesName && matchesSubject;

                    card.hidden = !visible;

                    if (visible) {
                        visibleCount++;
                    }
                });

                const unit = cards.length === 1 ? 'task' : 'tasks';

                count.textContent = visibleCount === cards.length
                    ? `${cards.length} ${unit}`
                    : `${visibleCount} of ${cards.length} ${unit}`;

                empty.hidden = visibleCount !== 0;
            }

            search.addEventListener('input', filterCompletedTasks);
            subject.addEventListener('change', filterCompletedTasks);
        })();
    </script>
@endsection