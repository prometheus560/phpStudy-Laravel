@extends ('layouts.app')

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
    @endphp

    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">
                    Completed Tasks
                </h2>

                @if ($total > 0)
                    <p class="text-sm text-gray-400 mt-2">
                        {{ $total }} completed
                    </p>
                @endif
            </div>

            <a href="{{ route('tasks.index') }}" class="btn-ghost">
                &larr; Back to Tasks
            </a>
        </div>

        @if ($total > 0)

            {{-- Search and subject filter --}}
            <div class="mb-5">
                @include('partials.list-search', [
                    'placeholder' => 'Search completed tasks...',
                    'filterAll' => 'All subjects',
                    'filters' => $subjectFilter,
                    'target' => '#completed-list',
                    'emptyText' => 'No matching tasks.',
                    'countTarget' => '#completed-count',
                    'unit' => 'task',
                ])
            </div>

            <div class="flex justify-end mb-4">
                <span id="completed-count" class="chip chip-muted">
                    {{ $total }} {{ $total === 1 ? 'task' : 'tasks' }}
                </span>
            </div>

            {{-- Preserve the order supplied by the controller --}}
            <div id="completed-list" class="flex flex-col gap-3">
                @foreach ($list as $task)
                    <div
                        data-search-item
                        data-name="{{ mb_strtolower($task->task_name) }}"
                        data-filter="{{ $task->subject_id }}"
                    >
                        @include('partials.task-card', [
                            'task' => $task,
                            'rank' => null,
                            'rankLabel' => '',
                            'badge' => 'Completed',
                            'badgeClass' => 'bg-green-500/15 text-green-400',
                        ])
                    </div>
                @endforeach
            </div>

        @else

            {{-- Empty state --}}
            <div class="glass text-center py-16 px-6">
                <div
                    class="mx-auto mb-5 flex items-center justify-center rounded-2xl"
                    style="
                        width: 56px;
                        height: 56px;
                        color: #4ade80;
                        background: rgba(34, 197, 94, 0.10);
                        border: 1px solid rgba(34, 197, 94, 0.20);
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="26"
                        height="26"
                        style="width: 26px; height: 26px; flex-shrink: 0;"
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
                </div>

                <h3 class="text-lg font-semibold text-white mb-6">
                    No completed tasks yet
                </h3>

                <a href="{{ route('tasks.index') }}" class="btn-primary">
                    View Tasks
                </a>
            </div>

        @endif

    </div>
@endsection