@extends('layouts.app')

@section('title', 'Find Subject')

@section('content')

<style>
    .hl { color: #c4b5fd; background: rgba(139,92,246,.20); border-radius: 4px; }
</style>

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-7">
        <h2 class="page-title">Find Subject</h2>
        <p class="page-sub">Type a letter and only the subjects that start with it stay. Search uses binary search.</p>
    </div>

    {{-- Search --}}
    <div class="glass p-6 mb-6">
        <form method="GET" action="{{ route('binary_search.index') }}" class="flex flex-wrap gap-3">
            <div class="control flex-1" style="min-width:220px">
                <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="subject-search" type="text" name="search" value="{{ $search }}"
                       placeholder="Type a subject name..." autocomplete="off"
                       class="f-input has-icon">
            </div>
            <button type="submit" class="btn-primary">Search</button>
        </form>

        @if ($message !== '')
            <div class="alert {{ str_starts_with($message, 'Subject found') ? 'alert-ok' : 'alert-err' }}" style="margin:16px 0 0" role="status">
                {{ $message }}
            </div>
        @endif
    </div>

    {{-- Subject list --}}
    <div class="glass p-6">

        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xl font-bold text-white">Your Subjects</h3>
            <span id="count" class="chip chip-blue">{{ $subjects->count() }} {{ $subjects->count() == 1 ? 'subject' : 'subjects' }}</span>
        </div>

        @if ($subjects->count() > 0)

            <div id="subject-list">
                @foreach ($groups as $letter => $items)
                    <div class="subject-group">
                        <p class="text-xs font-bold tracking-wider mt-5 mb-1" style="color:#7db4ff">{{ $letter }}</p>

                        @foreach ($items as $subject)
                            <div class="subject-row row-item flex items-center justify-between gap-4 py-3.5 px-2 -mx-2 rounded-lg"
                                 data-name="{{ mb_strtolower($subject->subject_name) }}">
                                <span class="subject-name text-white font-semibold text-lg truncate">{{ $subject->subject_name }}</span>
                                <span class="text-xs shrink-0" style="color:var(--muted)">
                                    ID {{ $subject->id }} &middot; {{ $subject->tasks_count }} {{ \Illuminate\Support\Str::plural('task', $subject->tasks_count) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div id="no-match" class="text-center py-12" style="display:none">
                <h4 class="font-semibold text-white">No subject found</h4>
                <p class="text-sm mt-1" style="color:var(--muted)">No subject starts with "<span id="no-match-q"></span>".</p>
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
    const list = document.getElementById('subject-list');
    if (!input || !list) return;

    const rows = Array.from(list.querySelectorAll('.subject-row'));
    const groups = Array.from(list.querySelectorAll('.subject-group'));
    const count = document.getElementById('count');
    const noMatch = document.getElementById('no-match');
    const noMatchQ = document.getElementById('no-match-q');
    const total = rows.length;

    // Names sorted A-Z (required for binary search)
    const sorted = rows
        .map(el => ({ el, key: el.dataset.name }))
        .sort((a, b) => (a.key < b.key ? -1 : a.key > b.key ? 1 : 0));

    // Binary search: first index whose name is >= the typed text
    function lowerBound(prefix) {
        let lo = 0, hi = sorted.length;
        while (lo < hi) {
            const mid = (lo + hi) >> 1;
            if (sorted[mid].key < prefix) lo = mid + 1;
            else hi = mid;
        }
        return lo;
    }

    // Show the typed part of the name highlighted
    function setName(row, q) {
        const el = row.querySelector('.subject-name');
        if (!el.dataset.full) el.dataset.full = el.textContent;
        const full = el.dataset.full;

        el.textContent = '';
        if (q) {
            const mark = document.createElement('span');
            mark.className = 'hl';
            mark.textContent = full.slice(0, q.length);
            el.appendChild(mark);
            el.appendChild(document.createTextNode(full.slice(q.length)));
        } else {
            el.textContent = full;
        }
    }

    function apply() {
        const raw = input.value.trim();
        const q = raw.toLowerCase();
        const shown = new Set();

        if (q === '') {
            rows.forEach(r => shown.add(r));
        } else {
            // Every subject that starts with the typed text sits right after lowerBound
            for (let i = lowerBound(q); i < sorted.length && sorted[i].key.startsWith(q); i++) {
                shown.add(sorted[i].el);
            }
        }

        rows.forEach(r => {
            const visible = shown.has(r);
            r.style.display = visible ? '' : 'none';
            setName(r, visible ? q : '');
        });

        groups.forEach(g => {
            const any = Array.from(g.querySelectorAll('.subject-row')).some(r => r.style.display !== 'none');
            g.style.display = any ? '' : 'none';
        });

        count.textContent = q === ''
            ? total + (total === 1 ? ' subject' : ' subjects')
            : shown.size + ' of ' + total;

        noMatch.style.display = shown.size === 0 ? '' : 'none';
        noMatchQ.textContent = raw;
    }

    input.addEventListener('input', apply);
    apply();
})();
</script>

@endsection