@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">My Tasks</h2>
        <p class="text-gray-400 text-sm mt-1">Create, manage, and track your study tasks.</p>
    </div>

    {{-- Add Task --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5 mb-6">
        <h3 class="text-lg font-bold text-white">Add New Task</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Add a task and organize it by subject, deadline, and priority.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-400 text-sm rounded-lg px-4 py-2 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($subjects->count() > 0)

            <form method="POST" action="{{ route('tasks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf

                {{-- Task Name --}}
                <div class="sm:col-span-2">
                    <label for="task_name" class="block text-sm font-semibold text-gray-200 mb-1.5">Task Name</label>
                    <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                           placeholder="Enter task name" maxlength="150" required
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                </div>

                {{-- Subject --}}
                <div>
                    <label for="subject_id" class="block text-sm font-semibold text-gray-200 mb-1.5">Subject</label>
                    <select id="subject_id" name="subject_id" required
                            class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                        <option value="">Select Subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Deadline --}}
                <div>
                    <label for="deadline" class="block text-sm font-semibold text-gray-200 mb-1.5">Deadline</label>
                    <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" required
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                </div>

                {{-- Priority --}}
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-200 mb-1.5">Priority</label>
                    <select id="priority" name="priority" required
                            class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                        <option value="High" @selected(old('priority') === 'High')>High</option>
                        <option value="Medium" @selected(old('priority', 'Medium') === 'Medium')>Medium</option>
                        <option value="Low" @selected(old('priority') === 'Low')>Low</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-blue-700 text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-blue-800">
                        Add Task
                    </button>
                </div>

            </form>

        @else

            <div class="text-center py-10">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-gray-400 text-sm mt-1">You need to create a subject before adding a task.</p>
                <div class="mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Add Subject
                    </a>
                </div>
            </div>

        @endif
    </div>

    {{-- Task List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">All Tasks</h3>
            <p class="text-gray-400 text-sm mt-1">
                {{ $tasks->count() }} {{ $tasks->count() == 1 ? 'task' : 'tasks' }} in your planner.
            </p>

            {{-- Instant Search --}}
            @if ($tasks->count() > 0)
                <div class="relative mt-4">
                    <input id="task-search" type="text" autocomplete="off"
                           placeholder="Search tasks..."
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                    <ul id="task-results"
                        class="hidden absolute z-10 w-full mt-1 bg-[#1b1b28] border border-[#2a2a38] rounded-lg shadow-lg overflow-hidden"></ul>
                </div>
            @endif
        </div>

        @if ($tasks->count() > 0)

            <div class="flex flex-col gap-4">

                @foreach ($tasks as $task)

                    <div id="task-{{ $task->id }}" class="border border-[#23232f] rounded-xl p-5 bg-[#1b1b28] transition">
                        <div class="flex flex-wrap items-start justify-between gap-5">

                            {{-- Task Information --}}
                            <div class="flex-1 min-w-[240px]">
                                <h4 class="text-blue-400 font-semibold text-lg mb-2">{{ $task->task_name }}</h4>

                                <p class="text-gray-400 text-sm mb-1">
                                    <span class="font-semibold text-gray-300">Subject:</span>
                                    {{ $task->subject->subject_name }}
                                </p>

                                <p class="text-gray-400 text-sm mb-1">
                                    <span class="font-semibold text-gray-300">Deadline:</span>
                                    {{ $task->deadline->format('F d, Y') }}
                                </p>

                                <p class="text-gray-400 text-sm mb-1">
                                    <span class="font-semibold text-gray-300">Priority:</span>
                                    @if ($task->priority === 'High')
                                        <span class="inline-block bg-rose-500/15 text-rose-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">High</span>
                                    @elseif ($task->priority === 'Medium')
                                        <span class="inline-block bg-yellow-500/15 text-yellow-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">Medium</span>
                                    @else
                                        <span class="inline-block bg-green-500/15 text-green-400 px-2.5 py-1 rounded-md text-xs font-bold ml-1">Low</span>
                                    @endif
                                </p>

                                <p class="text-gray-400 text-sm">
                                    <span class="font-semibold text-gray-300">Status:</span>
                                    @if ($task->status === 'Completed')
                                        <span class="text-green-400 font-bold ml-1">Completed</span>
                                    @else
                                        <span class="text-orange-400 font-bold ml-1">Pending</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center gap-2">

                                @if ($task->status === 'Completed')
                                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Pending">
                                        <button type="submit"
                                                class="bg-orange-500 text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-orange-600">
                                            Mark Pending
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit"
                                                class="bg-green-600 text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-green-700">
                                            Complete
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this task?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No tasks yet</h4>
                <p class="text-gray-400 text-sm mt-1">Add your first task above to start organizing your study work.</p>
            </div>

        @endif

    </div>

</div>

{{-- Prepare task data for the search (sorted by name, needed for binary search) --}}
@php
    $searchData = $tasks
        ->map(fn ($t) => [
            'id'      => $t->id,
            'name'    => $t->task_name,
            'subject' => $t->subject->subject_name,
        ])
        ->sortBy(fn ($t) => mb_strtolower($t['name']))
        ->values();
@endphp

<script>
(() => {
    const input = document.getElementById('task-search');
    const box = document.getElementById('task-results');
    if (!input) return;

    // Tasks sorted by name (required for binary search)
    const sorted = @json($searchData).map(t => ({ ...t, key: t.name.toLowerCase() }));

    // Binary search: find the first index whose key is >= the typed text
    function lowerBound(prefix) {
        let lo = 0, hi = sorted.length;
        while (lo < hi) {
            const mid = (lo + hi) >> 1;
            if (sorted[mid].key < prefix) lo = mid + 1;
            else hi = mid;
        }
        return lo;
    }

    // Collect every task that starts with the typed text (max 8)
    function search(q) {
        const out = [];
        for (let i = lowerBound(q);
             i < sorted.length && sorted[i].key.startsWith(q) && out.length < 8;
             i++) {
            out.push(sorted[i]);
        }
        return out;
    }

    // Scroll to the chosen task card and highlight it briefly
    function goToTask(id) {
        const card = document.getElementById('task-' + id);
        if (!card) return;
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.classList.add('ring-2', 'ring-blue-500');
        setTimeout(() => card.classList.remove('ring-2', 'ring-blue-500'), 1800);
        box.classList.add('hidden');
    }

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        box.innerHTML = '';

        if (!q) { box.classList.add('hidden'); return; }

        const results = search(q);

        if (results.length === 0) {
            const li = document.createElement('li');
            li.className = 'px-4 py-2 text-sm text-gray-500';
            li.textContent = 'No tasks found';
            box.appendChild(li);
        } else {
            results.forEach(t => {
                const li = document.createElement('li');
                li.className = 'px-4 py-2 text-sm text-gray-200 hover:bg-[#23232f] cursor-pointer';
                li.textContent = t.name + ' (' + t.subject + ')';
                li.addEventListener('click', () => goToTask(t.id));
                box.appendChild(li);
            });
        }

        box.classList.remove('hidden');
    });

    // Close the dropdown when clicking outside
    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !box.contains(e.target)) {
            box.classList.add('hidden');
        }
    });
})();
</script>

@endsection