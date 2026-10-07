@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')

@php
    $priorityBar = ['High' => 'bg-rose-500', 'Medium' => 'bg-yellow-500', 'Low' => 'bg-green-500'];
    $priorityBadge = [
        'High'   => 'bg-rose-500/15 text-rose-400',
        'Medium' => 'bg-yellow-500/15 text-yellow-400',
        'Low'    => 'bg-green-500/15 text-green-400',
    ];

    $doneCount    = $tasks->where('status', 'Completed')->count();
    $pendingCount = $tasks->count() - $doneCount;
    $overdueCount = $tasks->filter(fn ($t) => $t->is_overdue)->count();

    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-2.5 text-sm transition';
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">My Tasks</h2>
            <p class="text-gray-400 text-sm mt-1">Create, manage, and track your study tasks.</p>
        </div>
        <div class="flex gap-2 text-xs font-semibold">
            <span class="bg-orange-500/10 text-orange-300 border border-orange-500/20 rounded-lg px-3 py-1.5">{{ $pendingCount }} pending</span>
            <span class="bg-green-500/10 text-green-300 border border-green-500/20 rounded-lg px-3 py-1.5">{{ $doneCount }} done</span>
            @if ($overdueCount > 0)
                <span class="bg-rose-500/10 text-rose-300 border border-rose-500/20 rounded-lg px-3 py-1.5">{{ $overdueCount }} overdue</span>
            @endif
        </div>
    </div>

    {{-- Messages --}}
    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-6">{{ $errors->first() }}</div>
    @endif

    {{-- Add Task --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white">Add New Task</h3>
       

        @if ($subjects->count() > 0)

            <form method="POST" action="{{ route('tasks.store') }}" class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                @csrf

                <div class="sm:col-span-6">
                    <label for="task_name" class="block text-sm font-semibold text-gray-200 mb-1.5">Task name</label>
                    <input id="task_name" type="text" name="task_name" value="{{ old('task_name') }}"
                           placeholder="Subject name" maxlength="150" required class="{{ $field }}">
                </div>

                <div class="sm:col-span-2">
                    <label for="subject_id" class="block text-sm font-semibold text-gray-200 mb-1.5">Subject</label>
                    <select id="subject_id" name="subject_id" required class="{{ $field }}">
                        <option value="">Select subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="deadline" class="block text-sm font-semibold text-gray-200 mb-1.5">Deadline</label>
                    <input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" required class="{{ $field }}">
                </div>

                <div class="sm:col-span-2">
                    <label for="priority" class="block text-sm font-semibold text-gray-200 mb-1.5">Priority</label>
                    <select id="priority" name="priority" required class="{{ $field }}">
                        <option value="High" @selected(old('priority') === 'High')>High</option>
                        <option value="Medium" @selected(old('priority', 'Medium') === 'Medium')>Medium</option>
                        <option value="Low" @selected(old('priority') === 'Low')>Low</option>
                    </select>
                </div>

                <div class="sm:col-span-6">
                    <button type="submit"
                            class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                        + Add Task
                    </button>
                </div>
            </form>

        @else

            <div class="text-center py-8">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-gray-400 text-sm mt-1">You need to create a subject before adding a task.</p>
                <a href="{{ route('subjects.index') }}"
                   class="inline-block mt-4 bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500">Add Subject</a>
            </div>

        @endif
    </div>

    {{-- Task List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">All Tasks</h3>
            <p class="text-gray-400 text-sm mt-1">{{ $tasks->count() }} {{ $tasks->count() == 1 ? 'task' : 'tasks' }} in your planner.</p>

            @if ($tasks->count() > 0)

                {{-- Instant Search (Binary Search) --}}
                <div class="relative mt-4">
                    <input id="task-search" type="text" autocomplete="off" placeholder="Search tasks..." class="{{ $field }}">
                    <ul id="task-results"
                        class="hidden absolute z-10 w-full mt-1 bg-[#1b1b28] border border-[#2a2a38] rounded-xl shadow-lg overflow-hidden"></ul>
                </div>

                {{-- Filter tabs --}}
                <div class="flex flex-wrap gap-2 mt-4" id="filter-tabs">
                    @foreach ([['all', 'All', $tasks->count()], ['Pending', 'Pending', $pendingCount], ['overdue', 'Overdue', $overdueCount], ['Completed', 'Completed', $doneCount]] as [$key, $label, $count])
                        <button type="button" data-filter="{{ $key }}"
                                class="filter-tab text-sm font-medium rounded-lg px-3.5 py-1.5 border border-[#2a2a38] text-gray-400 hover:text-white hover:bg-white/5">
                            {{ $label }} <span class="opacity-60">{{ $count }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if ($tasks->count() > 0)

            <div class="flex flex-col gap-3" id="task-list">

                @foreach ($tasks as $task)
                    @php
                        $isDone  = $task->status === 'Completed';
                        $overdue = $task->is_overdue;
                        $days    = (int) today()->diffInDays($task->deadline->copy()->startOfDay(), false);
                        $dueTone = $isDone ? 'text-green-400' : ($overdue ? 'text-rose-400' : ($days <= 2 ? 'text-amber-400' : 'text-gray-400'));
                    @endphp

                    <div id="task-{{ $task->id }}"
                         data-status="{{ $isDone ? 'Completed' : 'Pending' }}"
                         data-overdue="{{ $overdue ? 1 : 0 }}"
                         class="task-card flex overflow-hidden rounded-xl border border-[#23232f] bg-[#1b1b28] transition {{ $isDone ? 'opacity-70' : '' }}">

                        <span class="w-1 shrink-0 {{ $priorityBar[$task->priority] ?? 'bg-gray-500' }}"></span>

                        <div class="flex-1 p-4 flex flex-wrap items-center justify-between gap-4">

                            <div class="flex-1 min-w-[220px]">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold {{ $isDone ? 'text-gray-500 line-through' : 'text-white' }}">{{ $task->task_name }}</h4>
                                    <span class="text-[11px] font-bold rounded-md px-2 py-0.5 {{ $priorityBadge[$task->priority] ?? 'bg-white/5 text-gray-400' }}">{{ $task->priority }}</span>
                                </div>

                                <p class="text-sm text-gray-400 mt-2 flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <span class="text-[11px] font-semibold uppercase tracking-wide bg-white/5 text-gray-300 rounded px-2 py-0.5">{{ $task->subject->subject_name }}</span>
                                    <span>{{ $task->deadline->format('M d, Y') }}</span>
                                    <span class="font-semibold {{ $dueTone }}">{{ $task->due_label }}</span>
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">

                                <form method="POST" action="{{ route('tasks.update', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $isDone ? 'Pending' : 'Completed' }}">
                                    <button type="submit"
                                            class="rounded-lg px-3.5 py-2 text-sm font-medium border {{ $isDone ? 'border-orange-500/40 text-orange-300 hover:bg-orange-500/10' : 'border-green-500/40 text-green-400 hover:bg-green-500/10' }}">
                                        {{ $isDone ? 'Reopen' : '✓ Complete' }}
                                    </button>
                                </form>

                                <button type="button" class="edit-btn rounded-lg px-3.5 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:text-white hover:bg-white/5"
                                        data-task="{{ json_encode([
                                            'id'         => $task->id,
                                            'task_name'  => $task->task_name,
                                            'subject_id' => $task->subject_id,
                                            'deadline'   => $task->deadline->format('Y-m-d'),
                                            'priority'   => $task->priority,
                                            'status'     => $task->status,
                                        ]) }}">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                      onsubmit="return confirm('Delete this task? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3.5 py-2 text-sm font-medium border border-red-500/30 text-red-400 hover:bg-red-500/10">Delete</button>
                                </form>

                            </div>
                        </div>
                    </div>
                @endforeach

                <p id="filter-empty" class="hidden text-center text-gray-500 text-sm py-10">No tasks in this view.</p>
            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No tasks yet</h4>
                <p class="text-gray-400 text-sm mt-1">Add your first task above to start organizing your study work.</p>
            </div>

        @endif
    </div>
</div>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-200">
        @csrf
        @method('PATCH')

        <h3 class="sm:col-span-2 text-lg font-bold text-white">Edit task</h3>

        <div class="sm:col-span-2">
            <label for="e_name" class="block text-sm font-semibold mb-1.5">Task name</label>
            <input id="e_name" name="task_name" maxlength="150" required class="{{ $field }}">
        </div>

        <div>
            <label for="e_subject" class="block text-sm font-semibold mb-1.5">Subject</label>
            <select id="e_subject" name="subject_id" required class="{{ $field }}">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="e_deadline" class="block text-sm font-semibold mb-1.5">Deadline</label>
            <input id="e_deadline" type="date" name="deadline" required class="{{ $field }}">
        </div>

        <div>
            <label for="e_priority" class="block text-sm font-semibold mb-1.5">Priority</label>
            <select id="e_priority" name="priority" class="{{ $field }}">
                <option>High</option><option>Medium</option><option>Low</option>
            </select>
        </div>

        <div>
            <label for="e_status" class="block text-sm font-semibold mb-1.5">Status</label>
            <select id="e_status" name="status" class="{{ $field }}">
                <option>Pending</option><option>Completed</option>
            </select>
        </div>

        <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
            <button type="button" id="edit-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">Save changes</button>
        </div>
    </form>
</dialog>

{{-- Search data (sorted by name, needed for binary search) --}}
@php
    $searchData = $tasks
        ->map(fn ($t) => ['id' => $t->id, 'name' => $t->task_name, 'subject' => $t->subject->subject_name])
        ->sortBy(fn ($t) => mb_strtolower($t['name']))
        ->values();
@endphp

<script>
(() => {
    // ---------- Filter tabs ----------
    const tabs  = document.querySelectorAll('.filter-tab');
    const cards = document.querySelectorAll('.task-card');
    const empty = document.getElementById('filter-empty');

    function setFilter(key) {
        let shown = 0;
        cards.forEach(c => {
            const ok = key === 'all'
                || (key === 'overdue' ? c.dataset.overdue === '1' : c.dataset.status === key);
            c.classList.toggle('hidden', !ok);
            if (ok) shown++;
        });
        tabs.forEach(t => {
            const on = t.dataset.filter === key;
            t.classList.toggle('bg-blue-600/15', on);
            t.classList.toggle('text-blue-300', on);
            t.classList.toggle('border-blue-500/40', on);
        });
        if (empty) empty.classList.toggle('hidden', shown > 0);
    }
    tabs.forEach(t => t.addEventListener('click', () => setFilter(t.dataset.filter)));
    if (tabs.length) setFilter('all');

    // ---------- Edit dialog ----------
    const dlg  = document.getElementById('edit-dialog');
    const form = document.getElementById('edit-form');

    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => {
        const t = JSON.parse(b.dataset.task);
        form.action = "{{ url('/tasks') }}/" + t.id;
        document.getElementById('e_name').value     = t.task_name;
        document.getElementById('e_subject').value  = t.subject_id;
        document.getElementById('e_deadline').value = t.deadline;
        document.getElementById('e_priority').value = t.priority;
        document.getElementById('e_status').value   = t.status;
        dlg.showModal();
    }));
    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

    // ---------- Instant search (Binary Search) ----------
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
        setFilter('all');
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
        if (!input.contains(e.target) && !box.contains(e.target)) box.classList.add('hidden');
    });
})();
</script>

@endsection