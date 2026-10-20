@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')

@php
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

@include('partials.page-kit')

<div class="pg">

    {{-- Header --}}
    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">My Subjects</h1>
            <p class="pg-sub">Manage your subjects and keep track of the tasks assigned to each one.</p>
        </div>

        <div class="pg-actions">
            <button type="button" data-open-add class="pg-btn main">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Subject
            </button>
        </div>
    </header>

    @if (session('success'))<div class="pg-flash" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="pg-flash err">{{ $errors->first() }}</div>@endif

    {{-- Readout strip --}}
    <div class="pg-stats">
        <div class="pg-stat" style="--c:#60a5fa"><b>{{ $subjectTotal }}</b><span>Subjects</span></div>
        <div class="pg-stat" style="--c:#fb923c"><b>{{ $sumPending }}</b><span>Pending tasks</span></div>
        <div class="pg-stat" style="--c:#4ade80"><b>{{ $sumDone }}</b><span>Completed tasks</span></div>
    </div>

    {{-- Toolbar --}}
    <div class="pg-section">
        <h3>Your Subjects</h3>
        <small>{{ $subjects->count() }} {{ $subjects->count() == 1 ? 'subject' : 'subjects' }}{{ $category ? ' in ' . $category : '' }}</small>
    </div>

    @if ($subjects->count() > 0)
        <div style="margin-bottom:12px">
            <input id="live-search" type="text" autocomplete="off" placeholder="Type a letter to find a subject..." class="pg-input">
        </div>
    @endif

    @if ($hasCategory && $totalSubjects > 0)
        <div class="pg-chips">
            <a href="{{ route('subjects.index') }}" class="pg-chip {{ $category === '' ? 'on' : '' }}">All <i>{{ $totalSubjects }}</i></a>

            @foreach ($categories as $c)
                @if (($categoryCounts[$c] ?? 0) > 0)
                    <a href="{{ route('subjects.index', ['category' => $c]) }}" class="pg-chip {{ $category === $c ? 'on' : '' }}">
                        {{ $c }} <i>{{ $categoryCounts[$c] }}</i>
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Subject cards --}}
    @if ($subjects->count() > 0)

        <div class="pg-sgrid">

            @foreach ($subjects as $subject)
                @php
                    $total = $counts[$subject->id]['total'];
                    $done  = $counts[$subject->id]['done'];
                    $pend  = $total - $done;
                    $pct   = $total > 0 ? round($done / $total * 100) : 0;
                    $cat   = $subject->getAttribute('category') ?: 'General';
                @endphp

                <div class="js-item pg-scard"
                     data-name="{{ mb_strtolower($subject->subject_name) }}" data-subject="" data-subject-id="">

                    <div style="display:flex;align-items:flex-start;gap:12px">
                        <span class="pg-letter {{ $tones[$cat] ?? $tones['General'] }}">
                            {{ strtoupper(mb_substr($subject->subject_name, 0, 1)) }}
                        </span>

                        <div style="min-width:0;flex:1">
                            <h4 class="pg-name" style="font-size:15.5px;font-weight:600">{{ $subject->subject_name }}</h4>
                            @if ($hasCategory)
                                <span class="pg-pill {{ $tones[$cat] ?? $tones['General'] }}" style="display:inline-block;margin-top:7px">{{ $cat }}</span>
                            @endif
                        </div>

                        {{-- Edit / Delete menu --}}
                        <details class="menu pg-menu">
                            <summary aria-label="Subject options">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
                            </summary>

                            <div>
                                <button type="button" class="edit-btn"
                                        data-subject="{{ json_encode(['id' => $subject->id, 'name' => $subject->subject_name, 'category' => $cat]) }}">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($subject->subject_name) }}?{{ $total > 0 ? ' This will also delete its ' . $total . ' ' . ($total == 1 ? 'task' : 'tasks') . '.' : '' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="d">Delete</button>
                                </form>
                            </div>
                        </details>
                    </div>

                    <div class="pg-prog">
                        @if ($total > 0)
                            <div>
                                <span>{{ $pend }} pending &middot; {{ $done }} done</span>
                                <b style="color:#a9c4ff">{{ $pct }}%</b>
                            </div>
                            <div class="pg-track"><i style="width: {{ $pct }}%"></i></div>
                        @else
                            <div><span>No tasks yet</span></div>
                            <div class="pg-track"></div>
                        @endif
                    </div>

                    <a href="{{ route('tasks.index') }}" class="pg-link">{{ $total }} {{ $total == 1 ? 'task' : 'tasks' }} &rarr;</a>
                </div>
            @endforeach

        </div>

        <p id="live-empty" class="hidden" style="text-align:center;color:#8d92ad;font-size:13.5px;padding:30px 0">No subject starts with what you typed. Try another letter.</p>

    @else

        <div class="pg-panel pg-empty">
            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
            <b>{{ $category ? 'No ' . $category . ' subjects' : 'No subjects yet' }}</b>
            <p>{{ $category ? 'Pick another category, or add a subject.' : 'Add your first subject to start planning your studies.' }}</p>
            <button type="button" data-open-add class="pg-btn main" style="margin-top:18px">+ Add Subject</button>
        </div>

    @endif

</div>

{{-- Add dialog --}}
<dialog id="add-dialog" class="bg-transparent p-0 m-auto w-full max-w-md backdrop:bg-black/70">
    <form method="POST" action="{{ route('subjects.store') }}" class="pgd">
        @csrf

        <div>
            <h3>Add new subject</h3>
            <p class="s">Add a subject to start organizing your study tasks.</p>
        </div>

        <div>
            <label for="a_name">Subject name</label>
            <input id="a_name" type="text" name="subject_name" value="{{ old('subject_name') }}"
                   placeholder="e.g. Web Development" maxlength="100" required class="pg-input">
        </div>

        @if ($hasCategory)
            <div>
                <label for="a_category">Category</label>
                <select id="a_category" name="category" class="pg-input">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" @selected(old('category', 'General') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="row">
            <button type="button" id="add-cancel" class="pg-btn ghost">Cancel</button>
            <button type="submit" class="pg-btn main">+ Add Subject</button>
        </div>
    </form>
</dialog>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-md backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="pgd">
        @csrf
        @method('PATCH')

        <h3>Edit subject</h3>

        <div>
            <label for="e_name">Subject name</label>
            <input id="e_name" name="subject_name" maxlength="100" required class="pg-input">
        </div>

        @if ($hasCategory)
            <div>
                <label for="e_category">Category</label>
                <select id="e_category" name="category" class="pg-input">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="row">
            <button type="button" id="edit-cancel" class="pg-btn ghost">Cancel</button>
            <button type="submit" class="pg-btn main">Save changes</button>
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