@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')

<style>
    details.menu > summary { list-style: none; cursor: pointer; }
    details.menu > summary::-webkit-details-marker { display: none; }
</style>

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

    // Task counts per subject (same query as before, now done once)
    $counts = [];
    $sumTotal = 0;
    $sumDone  = 0;

    foreach ($subjects as $s) {
        $t = $s->tasks_count;
        $d = $s->tasks()->whereIn('status', ['Completed', 'completed', 'Done', 'done'])->count();
        $counts[$s->id] = ['total' => $t, 'done' => $d];
        $sumTotal += $t;
        $sumDone  += $d;
    }

    $sumPending   = $sumTotal - $sumDone;
    $subjectTotal = $hasCategory ? $totalSubjects : $subjects->count();
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">My Subjects</h2>
            <p class="text-gray-400 text-sm mt-1">Manage your subjects and keep track of the tasks assigned to each one.</p>
        </div>

        <button type="button" data-open-add
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Subject
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-6">{{ $errors->first() }}</div>
    @endif

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-blue-600/15 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $subjectTotal }}</p>
                <p class="text-gray-400 text-sm mt-1">Subjects</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $sumPending }}</p>
                <p class="text-gray-400 text-sm mt-1">Pending tasks</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $sumDone }}</p>
                <p class="text-gray-400 text-sm mt-1">Completed tasks</p>
            </div>
        </div>

    </div>

    {{-- Toolbar --}}
    <div class="mb-5">
        <div class="flex items-baseline justify-between gap-3 mb-4">
            <h3 class="text-lg font-bold text-white">Your Subjects</h3>
            <p class="text-gray-500 text-sm">{{ $subjects->count() }} {{ $subjects->count() == 1 ? 'subject' : 'subjects' }}{{ $category ? ' in ' . $category : '' }}</p>
        </div>

        @if ($subjects->count() > 0)
            <div class="relative mb-4">
                <svg class="w-4 h-4 text-gray-500 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="live-search" type="text" autocomplete="off" placeholder="Type a letter to find a subject..."
                       class="{{ $field }}" style="padding-left:42px">
            </div>
        @endif

        @if ($hasCategory && $totalSubjects > 0)
            <div class="flex flex-wrap gap-2">
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

    {{-- Subject cards --}}
    @if ($subjects->count() > 0)

        <div class="grid gap-4" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">

            @foreach ($subjects as $subject)
                @php
                    $total = $counts[$subject->id]['total'];
                    $done  = $counts[$subject->id]['done'];
                    $pend  = $total - $done;
                    $pct   = $total > 0 ? round($done / $total * 100) : 0;
                    $cat   = $subject->getAttribute('category') ?: 'General';
                @endphp

                <div class="js-item flex flex-col border border-[#262636] rounded-2xl p-5 bg-gradient-to-b from-[#1d1d2c] to-[#161622] hover:border-blue-500/40 hover:shadow-lg hover:shadow-blue-900/20 transition"
                     data-name="{{ mb_strtolower($subject->subject_name) }}" data-subject="" data-subject-id="">

                    <div class="flex items-start gap-3">
                        <span class="w-11 h-11 shrink-0 rounded-xl border flex items-center justify-center text-lg font-bold {{ $tones[$cat] ?? $tones['General'] }}">
                            {{ strtoupper(mb_substr($subject->subject_name, 0, 1)) }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <h4 class="text-white font-semibold text-base leading-snug break-words">{{ $subject->subject_name }}</h4>
                            @if ($hasCategory)
                                <span class="inline-block mt-1.5 text-[11px] font-semibold border rounded-md px-2 py-0.5 {{ $tones[$cat] ?? $tones['General'] }}">{{ $cat }}</span>
                            @endif
                        </div>

                        {{-- Edit / Delete menu --}}
                        <details class="menu relative z-20 shrink-0">
                            <summary class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-white hover:bg-white/5" aria-label="Subject options">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
                            </summary>

                            <div class="absolute right-0 top-full mt-1 w-36 z-30 rounded-xl border border-[#2a2a38] bg-[#1a1a28] p-1 shadow-xl shadow-black/50">
                                <button type="button"
                                        class="edit-btn w-full text-left rounded-lg px-3 py-2 text-sm text-gray-200 hover:bg-white/5"
                                        data-subject="{{ json_encode(['id' => $subject->id, 'name' => $subject->subject_name, 'category' => $cat]) }}">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($subject->subject_name) }}?{{ $total > 0 ? ' This will also delete its ' . $total . ' ' . ($total == 1 ? 'task' : 'tasks') . '.' : '' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm text-red-400 hover:bg-red-500/10">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>

                    <div class="mt-5">
                        @if ($total > 0)
                            <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                                <span>{{ $pend }} pending &middot; {{ $done }} done</span>
                                <span class="font-semibold text-blue-300">{{ $pct }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500" style="width: {{ $pct }}%"></div>
                            </div>
                        @else
                            <p class="text-xs text-gray-500">No tasks yet</p>
                            <div class="h-1.5 rounded-full bg-white/5 mt-2"></div>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-t border-[#262636]">
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-400 hover:text-blue-300">
                            {{ $total }} {{ $total == 1 ? 'task' : 'tasks' }} &rarr;
                        </a>
                    </div>
                </div>
            @endforeach

        </div>

        <p id="live-empty" class="hidden text-center text-gray-500 text-sm py-10">No subject starts with what you typed. Try another letter.</p>

    @else

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl text-center py-16 px-6">
            <span class="w-14 h-14 mx-auto rounded-2xl bg-blue-600/15 border border-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </span>
            <h4 class="font-semibold text-white">{{ $category ? 'No ' . $category . ' subjects' : 'No subjects yet' }}</h4>
            <p class="text-gray-400 text-sm mt-1 mb-5">{{ $category ? 'Pick another category, or add a subject.' : 'Add your first subject to start planning your studies.' }}</p>
            <button type="button" data-open-add
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">
                + Add Subject
            </button>
        </div>

    @endif

</div>

{{-- Add dialog --}}
<dialog id="add-dialog" class="bg-transparent p-0 m-auto w-full max-w-md backdrop:bg-black/70">
    <form method="POST" action="{{ route('subjects.store') }}" class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 space-y-4 text-gray-200">
        @csrf

        <div>
            <h3 class="text-lg font-bold text-white">Add new subject</h3>
            <p class="text-gray-400 text-sm mt-1">Add a subject to start organizing your study tasks.</p>
        </div>

        <div>
            <label for="a_name" class="block text-sm font-semibold mb-1.5">Subject name</label>
            <input id="a_name" type="text" name="subject_name" value="{{ old('subject_name') }}"
                   placeholder="e.g. Web Development" maxlength="100" required class="{{ $field }}">
        </div>

        @if ($hasCategory)
            <div>
                <label for="a_category" class="block text-sm font-semibold mb-1.5">Category</label>
                <select id="a_category" name="category" class="{{ $field }}">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" @selected(old('category', 'General') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" id="add-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">+ Add Subject</button>
        </div>
    </form>
</dialog>

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
    const add  = document.getElementById('add-dialog');

    // Add dialog
    document.querySelectorAll('[data-open-add]').forEach(b => b.addEventListener('click', () => {
        add.showModal();
        document.getElementById('a_name').focus();
    }));
    document.getElementById('add-cancel').addEventListener('click', () => add.close());
    add.addEventListener('click', e => { if (e.target === add) add.close(); });

    // Edit dialog
    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => {
        const s = JSON.parse(b.dataset.subject);
        form.action = "{{ url('/subjects') }}/" + s.id;
        document.getElementById('e_name').value = s.name;
        const cat = document.getElementById('e_category');
        if (cat) cat.value = s.category;

        const menu = b.closest('details');
        if (menu) menu.removeAttribute('open');

        dlg.showModal();
    }));

    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

    // Close the "..." menus when clicking elsewhere
    document.addEventListener('click', e => {
        document.querySelectorAll('details.menu[open]').forEach(d => {
            if (!d.contains(e.target)) d.removeAttribute('open');
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('details.menu[open]').forEach(d => d.removeAttribute('open'));
        }
    });
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