@extends('layouts.app')

@section('title', 'Find Subject')

@section('content')

@php
    // Data for the picker (the controller already sorts the subjects A-Z)
    $subjectData = $subjects->map(fn ($s) => [
        'id'      => $s->id,
        'name'    => $s->subject_name,
        'tasks'   => (int) $s->tasks_count,
        'pending' => (int) $s->pending_tasks_count,
    ])->values();
@endphp

<style>
    .hl { color: #c4b5fd; background: rgba(139,92,246,.20); border-radius: 4px; }

    /* The pop-up list under the search box */
    #subject-options {
        display: none; position: absolute; left: 0; right: 0; top: 100%; margin-top: 8px;
        max-height: 288px; overflow-y: auto; padding: 6px; list-style: none; z-index: 40;
        background: #12111f; border: 1px solid rgba(190,175,255,.18); border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0,0,0,.6);
    }
    #subject-options li { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 12px; border-radius: 10px; cursor: pointer; color: #e5e7f0; }
    #subject-options li .meta { font-size: 12px; color: var(--muted); flex-shrink: 0; }
    #subject-options li.active { background: rgba(139,92,246,.16); }
    #subject-options li.empty { cursor: default; color: var(--muted); justify-content: center; }
    #toggle-btn { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; background: transparent; color: #7a7f95; border-radius: 9px; cursor: pointer; display: grid; place-items: center; }
    #toggle-btn:hover { color: #fff; background: rgba(255,255,255,.06); }
    #toggle-btn svg { width: 18px; height: 18px; }
    #subject-filter { width: 200px; flex: none; }
    @media (max-width: 560px) { #subject-filter { width: 100%; } }
</style>

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-7">
        <h2 class="page-title">Find Subject</h2>
        <p class="page-sub">Click the box or type a letter, then pick a subject. Search uses binary search.</p>
    </div>

    {{-- Search bar: raised above the card below so the pop-up list is never hidden --}}
    <div class="glass p-6 mb-6" style="position: relative; z-index: 30;">
        <div class="flex flex-wrap gap-3">

            <div class="control flex-1" style="min-width: 220px">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>

                <input id="subject-search" type="text" autocomplete="off"
                       role="combobox" aria-expanded="false" aria-controls="subject-options"
                       placeholder="Type a letter to find a subject..."
                       class="f-input has-icon" style="padding-right: 52px;">

                {{-- Opens the list, or clears the selected subject --}}
                <button type="button" id="toggle-btn" aria-label="Open list">
                    <svg id="icon-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    <svg id="icon-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>

                <ul id="subject-options" role="listbox"></ul>
            </div>

            <select id="subject-filter" class="f-input" aria-label="Filter subjects">
                <option value="all">All subjects</option>
                <option value="pending">With pending tasks</option>
                <option value="none">No tasks yet</option>
            </select>

        </div>
    </div>

    {{-- Selected subject --}}
    <div class="glass p-6">

        @if ($subjects->count() > 0)

            <div id="selected-empty" class="text-center py-12">
                <h4 class="font-semibold text-white">No subject selected yet</h4>
                <p class="text-sm mt-1" style="color:var(--muted)">Click the box above and choose a subject. Its details will show here.</p>
            </div>

            <div id="selected-card" style="display:none">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <span class="chip chip-blue">Selected subject</span>
                        <h3 id="sel-name" class="text-3xl font-bold text-white mt-3 truncate"></h3>
                        <p id="sel-id" class="text-sm mt-1" style="color:var(--muted)"></p>
                    </div>
                    <span id="sel-percent" class="chip chip-blue"></span>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-6">
                    <div class="rounded-xl p-4 border border-white/10 bg-white/[.03]">
                        <p id="sel-tasks" class="text-2xl font-bold text-white"></p>
                        <p class="text-xs mt-1" style="color:var(--muted)">Tasks</p>
                    </div>
                    <div class="rounded-xl p-4 border border-white/10 bg-white/[.03]">
                        <p id="sel-pending" class="text-2xl font-bold text-white"></p>
                        <p class="text-xs mt-1" style="color:var(--muted)">Pending</p>
                    </div>
                    <div class="rounded-xl p-4 border border-white/10 bg-white/[.03]">
                        <p id="sel-done" class="text-2xl font-bold text-white"></p>
                        <p class="text-xs mt-1" style="color:var(--muted)">Done</p>
                    </div>
                </div>

                <div class="bar mt-5"><i id="sel-bar" style="width:0%"></i></div>

                <p id="sel-search" class="text-sm mt-4" style="color:var(--muted)"></p>

                <div class="mt-5">
                    <a href="{{ route('tasks.index') }}" class="btn-ghost">View tasks &rarr;</a>
                </div>
            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No subjects yet</h4>
                <p class="text-sm mt-1" style="color:var(--muted)">You don't have any subjects yet.</p>
                <div class="mt-5">
                    <a href="{{ route('subjects.index') }}" class="btn-primary" style="height:44px">Add Subject</a>
                </div>
            </div>

        @endif
    </div>

</div>

<script>
(() => {
    const input = document.getElementById('subject-search');
    if (!input) return;

    const box = document.getElementById('subject-options');
    const toggle = document.getElementById('toggle-btn');
    const filterSelect = document.getElementById('subject-filter');
    const iconChev = document.getElementById('icon-chev');
    const iconX = document.getElementById('icon-x');
    const $ = (id) => document.getElementById(id);

    // Subjects sorted A-Z (required for binary search)
    const items = @json($subjectData)
        .map(s => ({ ...s, key: s.name.toLowerCase() }))
        .sort((a, b) => (a.key < b.key ? -1 : a.key > b.key ? 1 : 0));

    // No subjects yet: nothing to pick from
    if (items.length === 0) return;

    let selected = null;  
    let matches = items;   
    let active = -1;       
    let isOpen = false;
    let currentQ = '';    

   
    function lowerBound(prefix) {
        let lo = 0, hi = items.length;
        while (lo < hi) {
            const mid = (lo + hi) >> 1;
            if (items[mid].key < prefix) lo = mid + 1;
            else hi = mid;
        }
        return lo;
    }

    // Binary search for an exact name; also counts the steps
    function binarySearch(key) {
        let lo = 0, hi = items.length - 1, steps = 0;
        while (lo <= hi) {
            const mid = (lo + hi) >> 1;
            steps++;
            if (items[mid].key === key) return { index: mid, steps };
            if (items[mid].key < key) lo = mid + 1;
            else hi = mid - 1;
        }
        return { index: -1, steps };
    }

    // The dropdown on the right of the search box
    function passesFilter(s) {
        const f = filterSelect.value;
        if (f === 'pending') return s.pending > 0;
        if (f === 'none') return s.tasks === 0;
        return true;
    }

    // Subjects that start with the typed text (binary search), then the dropdown filter
    function filter(q) {
        let found = items;
        if (q) {
            found = [];
            for (let i = lowerBound(q); i < items.length && items[i].key.startsWith(q); i++) found.push(items[i]);
        }
        return found.filter(passesFilter);
    }

    function plural(n, word) { return n + ' ' + word + (n === 1 ? '' : 's'); }

    function render(q) {
        box.replaceChildren();

        if (matches.length === 0) {
            const li = document.createElement('li');
            li.className = 'empty';
            li.textContent = q ? 'No subject starts with "' + q + '"' : 'No subjects in this filter';
            box.appendChild(li);
            return;
        }

        matches.forEach((s, i) => {
            const li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.dataset.index = i;
            if (i === active) li.classList.add('active');

            const name = document.createElement('span');
            if (q) {
                const mark = document.createElement('span');
                mark.className = 'hl';
                mark.textContent = s.name.slice(0, q.length);
                name.appendChild(mark);
                name.appendChild(document.createTextNode(s.name.slice(q.length)));
            } else {
                name.textContent = s.name;
            }

            const meta = document.createElement('span');
            meta.className = 'meta';
            meta.textContent = 'ID ' + s.id + ' \u00B7 ' + plural(s.tasks, 'task');

            li.appendChild(name);
            li.appendChild(meta);
            box.appendChild(li);
        });
    }

    function openList(showAll) {
        const q = showAll ? '' : input.value.trim().toLowerCase();
        currentQ = q;
        matches = filter(q);
        active = selected ? matches.indexOf(selected) : -1;
        render(q);
        box.style.display = 'block';
        isOpen = true;
        input.setAttribute('aria-expanded', 'true');
        const row = box.querySelector('li.active');
        if (row) row.scrollIntoView({ block: 'nearest' });
    }

    function closeList() {
        box.style.display = 'none';
        isOpen = false;
        input.setAttribute('aria-expanded', 'false');
    }

    function showSelected() {
        const s = selected;
        $('selected-empty').style.display = s ? 'none' : '';
        $('selected-card').style.display = s ? '' : 'none';
        iconChev.style.display = s ? 'none' : '';
        iconX.style.display = s ? '' : 'none';
        toggle.setAttribute('aria-label', s ? 'Clear selection' : 'Open list');
        if (!s) return;

        const done = s.tasks - s.pending;
        const percent = s.tasks > 0 ? Math.round(done / s.tasks * 100) : 0;
        const found = binarySearch(s.key);

        $('sel-name').textContent = s.name;
        $('sel-id').textContent = 'ID ' + s.id;
        $('sel-tasks').textContent = s.tasks;
        $('sel-pending').textContent = s.pending;
        $('sel-done').textContent = done;
        $('sel-percent').textContent = percent + '% done';
        $('sel-bar').style.width = percent + '%';
        $('sel-search').textContent = 'Binary search found it at position ' + (found.index + 1)
            + ' of ' + items.length + ' in ' + plural(found.steps, 'step') + '.';
    }

    function choose(s) {
        selected = s;
        input.value = s.name;
        closeList();
        showSelected();
    }

    function clearSelection() {
        selected = null;
        input.value = '';
        showSelected();
    }

    function move(delta) {
        if (!isOpen) { openList(true); return; }
        if (matches.length === 0) return;
        active = Math.max(0, Math.min(matches.length - 1, active + delta));
        render(currentQ);
        const row = box.querySelector('li.active');
        if (row) row.scrollIntoView({ block: 'nearest' });
    }

    // Clicking the box opens the full list (or the filtered one if the user typed)
    input.addEventListener('focus', () => {
        const showAll = selected && input.value === selected.name;
        openList(showAll);
        if (showAll) input.select();
    });
    input.addEventListener('click', () => {
        if (!isOpen) {
            const showAll = selected && input.value === selected.name;
            openList(showAll);
            if (showAll) input.select();
        }
    });

    input.addEventListener('input', () => openList(false));

    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
        else if (e.key === 'Enter') {
            // Enter picks the highlighted row, or the first match if none is highlighted
            if (isOpen && matches.length > 0) {
                e.preventDefault();
                choose(matches[active >= 0 ? active : 0]);
            }
        }
        else if (e.key === 'Escape') {
            closeList();
            if (selected) input.value = selected.name;
        }
    });

    input.addEventListener('blur', closeList);

    // Keep the box focused while the user clicks inside the list or on the little button
    box.addEventListener('mousedown', (e) => e.preventDefault());
    toggle.addEventListener('mousedown', (e) => e.preventDefault());

    box.addEventListener('mousemove', (e) => {
        const li = e.target.closest('li[data-index]');
        if (!li) return;
        const i = parseInt(li.dataset.index, 10);
        if (i !== active) {
            const prev = box.querySelector('li.active');
            if (prev) prev.classList.remove('active');
            li.classList.add('active');
            active = i;
        }
    });

    box.addEventListener('click', (e) => {
        const li = e.target.closest('li[data-index]');
        if (li) choose(matches[parseInt(li.dataset.index, 10)]);
    });

    toggle.addEventListener('click', () => {
        if (selected) {
            clearSelection();
            input.focus();
        } else if (isOpen) {
            closeList();
        } else {
            input.focus();
            openList(true);
        }
    });

    // Changing the dropdown on the right re-filters the list and shows it
    filterSelect.addEventListener('change', () => {
        input.focus();
        openList(selected && input.value === selected.name);
    });

    showSelected();
})();
</script>

@endsection