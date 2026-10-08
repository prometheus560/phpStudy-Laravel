@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')

@php
    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-3 text-sm transition';

    $tones = [
        'General'  => 'bg-white/5 text-gray-300 border-white/10',
        'Major'    => 'bg-blue-500/10 text-blue-300 border-blue-500/25',
        'Minor'    => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/25',
        'Elective' => 'bg-purple-500/10 text-purple-300 border-purple-500/25',
        'PE'       => 'bg-green-500/10 text-green-300 border-green-500/25',
        'Other'    => 'bg-amber-500/10 text-amber-300 border-amber-500/25',
    ];

    $totalSubjects = array_sum($categoryCounts);
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">My Subjects</h2>
        <p class="text-gray-400 text-sm mt-1">Manage your subjects and keep track of the tasks assigned to each one.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    {{-- Add Subject --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white">Add New Subject</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Add a subject to start organizing your study tasks.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('subjects.store') }}" class="flex flex-wrap gap-3">
            @csrf

            <input type="text" name="subject_name" value="{{ old('subject_name') }}"
                   placeholder="e.g. Web Development" maxlength="100" required
                   class="flex-1 min-w-[220px] {{ $field }}">

            @if ($hasCategory)
                <select name="category" class="sm:w-44 {{ $field }}" aria-label="Category">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" @selected(old('category', 'General') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                + Add Subject
            </button>
        </form>
    </div>

    {{-- Subject List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">Your Subjects</h3>
            <p class="text-gray-400 text-sm mt-1">{{ $subjects->count() }} {{ $subjects->count() == 1 ? 'subject' : 'subjects' }}{{ $category ? ' in ' . $category : ' in your planner' }}.</p>

            {{-- Category filter --}}
            @if ($hasCategory && $totalSubjects > 0)
                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="text-sm font-medium rounded-lg px-3.5 py-1.5 border {{ $category === '' ? 'bg-blue-600/15 text-blue-300 border-blue-500/40' : 'border-[#2a2a38] text-gray-400 hover:text-white hover:bg-white/5' }}">
                        All <span class="opacity-60">{{ $totalSubjects }}</span>
                    </a>

                    @foreach ($categories as $c)
                        @if (($categoryCounts[$c] ?? 0) > 0)
                            <a href="{{ route('subjects.index', ['category' => $c]) }}"
                               class="text-sm font-medium rounded-lg px-3.5 py-1.5 border {{ $category === $c ? 'bg-blue-600/15 text-blue-300 border-blue-500/40' : 'border-[#2a2a38] text-gray-400 hover:text-white hover:bg-white/5' }}">
                                {{ $c }} <span class="opacity-60">{{ $categoryCounts[$c] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        @if ($subjects->count() > 0)

            <div class="mb-5">
                <input id="live-search" type="text" autocomplete="off" placeholder="Type a letter to find a subject..." class="{{ $field }}">
            </div>

            <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">

                @foreach ($subjects as $subject)
                    @php
                        $total = $subject->tasks_count;
                        $done  = $subject->tasks()->whereIn('status', ['Completed', 'completed', 'Done', 'done'])->count();
                        $pend  = $total - $done;
                        $pct   = $total > 0 ? round($done / $total * 100) : 0;
                        $cat   = $subject->getAttribute('category') ?: 'General';
                    @endphp

                    <div class="js-item flex flex-col border border-[#23232f] rounded-2xl p-5 bg-[#1b1b28] hover:border-blue-500/30 transition"
                         data-name="{{ mb_strtolower($subject->subject_name) }}" data-subject="" data-subject-id="">

                        <div class="flex items-start justify-between gap-3">
                            <h4 class="text-white font-semibold text-lg leading-snug break-words">{{ $subject->subject_name }}</h4>
                            <span class="text-xs font-semibold text-blue-300 bg-blue-500/10 border border-blue-500/20 rounded-md px-2 py-0.5 shrink-0">{{ $pct }}%</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if ($hasCategory)
                                <span class="text-[11px] font-semibold border rounded-md px-2 py-0.5 {{ $tones[$cat] ?? $tones['General'] }}">{{ $cat }}</span>
                            @endif
                            <span class="text-xs text-gray-500">ID {{ $subject->id }}</span>
                        </div>

                        <p class="text-gray-400 text-sm mt-3">{{ $pend }} pending &middot; {{ $done }} done</p>

                        <div class="h-1.5 rounded-full bg-white/5 overflow-hidden mt-3">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500" style="width: {{ $pct }}%"></div>
                        </div>

                        <div class="flex items-center justify-between gap-2 mt-5 pt-4 border-t border-[#23232f]">
                            <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300">
                                {{ $total }} {{ $total == 1 ? 'task' : 'tasks' }} &rarr;
                            </a>

                            <div class="flex items-center gap-2">
                                <button type="button"
                                        class="edit-btn rounded-lg px-3 py-1.5 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:text-white hover:bg-white/5"
                                        data-subject="{{ json_encode(['id' => $subject->id, 'name' => $subject->subject_name, 'category' => $cat]) }}">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($subject->subject_name) }}?{{ $total > 0 ? ' This will also delete its ' . $total . ' ' . ($total == 1 ? 'task' : 'tasks') . '.' : '' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-sm font-medium border border-red-500/30 text-red-400 hover:bg-red-500/10">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <p id="live-empty" class="hidden text-center text-gray-500 text-sm py-10">No subject starts with what you typed. Try another letter.</p>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">{{ $category ? 'No ' . $category . ' subjects' : 'No subjects yet' }}</h4>
                <p class="text-gray-400 text-sm mt-1">{{ $category ? 'Pick another category, or add a subject above.' : 'Add your first subject above to start planning your studies.' }}</p>
            </div>

        @endif

    </div>

</div>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-md backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 space-y-4 text-gray-200">
        @csrf
        @method('PATCH')

        <h3 class="text-lg font-bold text-white">Edit subject</h3>

        <div>
            <label for="e_name" class="block text-sm font-semibold mb-1.5">Subject name</label>
            <input id="e_name" name="subject_name" maxlength="100" required class="{{ $field }}">
        </div>

        @if ($hasCategory)
            <div>
                <label for="e_category" class="block text-sm font-semibold mb-1.5">Category</label>
                <select id="e_category" name="category" class="{{ $field }}">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" id="edit-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">Save changes</button>
        </div>
    </form>
</dialog>

<script>
(() => {
    const dlg  = document.getElementById('edit-dialog');
    const form = document.getElementById('edit-form');

    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => {
        const s = JSON.parse(b.dataset.subject);
        form.action = "{{ url('/subjects') }}/" + s.id;
        document.getElementById('e_name').value = s.name;
        const cat = document.getElementById('e_category');
        if (cat) cat.value = s.category;
        dlg.showModal();
    }));

    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
})();
</script>

<script>
(() => {
    const input  = document.getElementById('live-search');
    const select = document.getElementById('live-subject');
    const clear  = document.getElementById('live-clear');
    const empty  = document.getElementById('live-empty');
    const count  = document.getElementById('live-count');
    const items  = [...document.querySelectorAll('.js-item')];

    if (!input) return;

    function apply() {
        const q = input.value.trim().toLowerCase();
        const subjectId = select ? select.value : '';
        let shown = 0;

        // Look at the items one by one (Linear Search)
        for (let i = 0; i < items.length; i++) {
            const item = items[i];

            const textOk = q === ''
                || item.dataset.name.startsWith(q)
                || item.dataset.subject.startsWith(q);

            const subjectOk = subjectId === '' || item.dataset.subjectId === subjectId;

            const ok = textOk && subjectOk;
            item.classList.toggle('hidden', !ok);
            if (ok) shown++;
        }

        if (empty) empty.classList.toggle('hidden', shown > 0 || items.length === 0);
        if (clear) clear.classList.toggle('hidden', q === '' && subjectId === '');
        if (count && count.dataset.unit) {
            count.textContent = shown + ' ' + count.dataset.unit + (shown === 1 ? '' : 's');
        }
    }

    input.addEventListener('input', apply);
    if (select) select.addEventListener('change', apply);
    if (input.form) input.form.addEventListener('submit', e => e.preventDefault());

    if (clear) {
        clear.addEventListener('click', () => {
            input.value = '';
            if (select) select.value = '';
            apply();
            input.focus();
        });
    }

    apply();
})();
</script>

@endsection