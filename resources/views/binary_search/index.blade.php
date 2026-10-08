@extends('layouts.app')

@section('title', 'Find Subject')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Find Subject</h2>
        <p class="text-gray-400 text-sm mt-1">Search for a subject using Binary Search.</p>
    </div>

    {{-- Search --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5 mb-6">
        <h3 class="text-lg font-bold text-white mb-1">Search Your Subject</h3>
        <p class="text-gray-400 text-sm mb-5">Start typing and matching subjects pop up instantly.</p>

        <form method="GET" action="{{ route('binary_search.index') }}" class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input id="subject-search" type="text" name="search" value="{{ $search }}"
                       placeholder="Enter subject name" required autocomplete="off"
                       class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-3 text-sm">

                {{-- Instant results (pop up) --}}
                <ul id="subject-results"
                    class="hidden absolute z-10 w-full mt-1 bg-[#1b1b28] border border-[#2a2a38] rounded-lg shadow-lg overflow-hidden"></ul>
            </div>

            <button type="submit"
                    class="bg-blue-700 text-white rounded-lg px-5 py-3 text-sm font-medium hover:bg-blue-800">
                Search
            </button>
        </form>

        @if ($message !== '')
            <div class="mt-5 bg-blue-500/10 text-blue-300 rounded-lg px-4 py-3 font-semibold text-sm">
                {{ $message }}
            </div>
        @endif

        {{-- Steps the search took --}}
        @if (count($steps) > 0)
            <details class="mt-4" open>
                <summary class="cursor-pointer text-sm font-semibold text-blue-400">
                    See the steps ({{ count($steps) }} {{ count($steps) == 1 ? 'check' : 'checks' }} instead of looking at all {{ $subjects->count() }})
                </summary>

                <p class="text-gray-400 text-sm mt-3">
                    Binary Search looks at the middle of the sorted list and throws away the half that
                    can't contain your subject. It repeats until it finds it or runs out of list.
                </p>

                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 border-b border-[#23232f]">
                                <th class="py-2 pr-3 font-semibold">Step</th>
                                <th class="py-2 pr-3 font-semibold">Searching</th>
                                <th class="py-2 pr-3 font-semibold">Middle</th>
                                <th class="py-2 font-semibold">What happened</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-300">
                            @foreach ($steps as $i => $step)
                                <tr class="border-b border-[#23232f] last:border-0">
                                    <td class="py-2 pr-3">{{ $i + 1 }}</td>
                                    <td class="py-2 pr-3 whitespace-nowrap">{{ $step['low'] }} to {{ $step['high'] }}</td>
                                    <td class="py-2 pr-3">#{{ $step['mid'] }} {{ $step['name'] }}</td>
                                    <td class="py-2">{{ $step['result'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </div>

    {{-- Subject list, grouped by first letter --}}
    @php
        $letters = $subjects->groupBy(fn ($s) => mb_strtoupper(mb_substr($s->subject_name, 0, 1)));
    @endphp

    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">
        <h3 class="text-lg font-bold text-white mb-1">Your Subjects</h3>
        <p class="text-gray-400 text-sm mb-4">Subjects available for searching, grouped by first letter.</p>

        @if ($subjects->count() > 0)

            @foreach ($letters as $letter => $items)
                <p class="text-xs font-bold text-blue-400 mt-4 mb-1 first:mt-0">{{ $letter }}</p>

                <div class="flex flex-col divide-y divide-[#23232f]">
                    @foreach ($items as $subject)
                        <div class="py-3 subject-row transition flex items-center justify-between gap-3"
                             data-name="{{ mb_strtolower($subject->subject_name) }}">
                            <strong class="text-white">{{ $subject->subject_name }}</strong>
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $subject->getAttribute('category') ? $subject->getAttribute('category') . ' · ' : '' }}ID {{ $subject->id }} &middot; {{ $subject->tasks_count }} {{ $subject->tasks_count == 1 ? 'task' : 'tasks' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endforeach

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No subjects yet</h4>
                <p class="text-gray-400 text-sm mt-1">You don't have any subjects yet.</p>

                <div class="mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Add Subject
                    </a>
                </div>
            </div>

        @endif
    </div>

</div>

{{-- Subjects sorted A-Z (required for binary search) --}}
@php
    $searchData = $subjects
        ->map(fn ($s) => [
            'id'      => $s->id,
            'name'    => $s->subject_name,
            'tasks'   => $s->tasks_count,
            'pending' => $s->pending_count,
            'category' => $s->getAttribute('category'),
        ])
        ->sortBy(fn ($s) => mb_strtolower($s['name']))
        ->values();
@endphp

<script>
(() => {
    const input = document.getElementById('subject-search');
    const box = document.getElementById('subject-results');
    if (!input) return;

    // Subjects sorted A-Z
    const sorted = @json($searchData).map(s => ({ ...s, key: s.name.toLowerCase() }));

    let results = [];
    let active = -1;

    // Binary search: first index whose key is >= the typed text
    function lowerBound(prefix) {
        let lo = 0, hi = sorted.length;
        while (lo < hi) {
            const mid = (lo + hi) >> 1;
            if (sorted[mid].key < prefix) lo = mid + 1;
            else hi = mid;
        }
        return lo;
    }

    // Every subject that starts with the typed text (max 8)
    function search(q) {
        const out = [];
        for (let i = lowerBound(q);
             i < sorted.length && sorted[i].key.startsWith(q) && out.length < 8;
             i++) {
            out.push(sorted[i]);
        }
        return out;
    }

    // Highlight the chosen subject in the list below
    function highlight(key) {
        document.querySelectorAll('.subject-row').forEach(row => {
            row.classList.remove('bg-blue-500/10');
            if (row.dataset.name === key) {
                row.classList.add('bg-blue-500/10');
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    function choose(s) {
        input.value = s.name;
        box.classList.add('hidden');
        highlight(s.key);
    }

    function paintActive() {
        [...box.children].forEach((li, i) => li.classList.toggle('bg-[#23232f]', i === active));
    }

    function render() {
        const q = input.value.trim().toLowerCase();
        box.innerHTML = '';
        active = -1;

        if (!q) { box.classList.add('hidden'); return; }

        results = search(q);

        if (results.length === 0) {
            const li = document.createElement('li');
            li.className = 'px-4 py-3 text-sm text-gray-500';
            li.textContent = 'No subject starts with "' + input.value.trim() + '"';
            box.appendChild(li);
        } else {
            results.forEach(s => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-[#23232f]';

                // First letter badge
                const badge = document.createElement('span');
                badge.className = 'w-8 h-8 shrink-0 rounded-lg bg-blue-500/15 text-blue-300 font-bold text-sm flex items-center justify-center';
                badge.textContent = s.name.charAt(0).toUpperCase();

                // Name with the typed letters in bold, then the task count
                const mid = document.createElement('div');
                mid.className = 'flex-1 min-w-0';

                const name = document.createElement('div');
                name.className = 'text-sm text-gray-200 truncate';
                const bold = document.createElement('b');
                bold.className = 'text-white';
                bold.textContent = s.name.slice(0, q.length);
                name.appendChild(bold);
                name.appendChild(document.createTextNode(s.name.slice(q.length)));

                const meta = document.createElement('div');
                meta.className = 'text-xs text-gray-500';
                meta.textContent = s.tasks + (s.tasks === 1 ? ' task' : ' tasks') + ' · ' + s.pending + ' pending';

                mid.appendChild(name);
                mid.appendChild(meta);

                // ID
                const id = document.createElement('span');
                id.className = 'text-xs text-gray-400 shrink-0';
                id.textContent = (s.category ? s.category + ' · ' : '') + 'ID ' + s.id;

                li.appendChild(badge);
                li.appendChild(mid);
                li.appendChild(id);
                li.addEventListener('click', () => choose(s));
                box.appendChild(li);
            });
        }

        box.classList.remove('hidden');
    }

    input.addEventListener('input', render);
    input.addEventListener('focus', () => { if (input.value.trim()) render(); });

    // Arrow keys, Enter and Escape
    input.addEventListener('keydown', e => {
        if (box.classList.contains('hidden') || results.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            active = (active + 1) % results.length;
            paintActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            active = (active - 1 + results.length) % results.length;
            paintActive();
        } else if (e.key === 'Enter' && active >= 0) {
            e.preventDefault();
            choose(results[active]);
        } else if (e.key === 'Escape') {
            box.classList.add('hidden');
        }
    });

    // Close the pop up when clicking outside
    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !box.contains(e.target)) {
            box.classList.add('hidden');
        }
    });
})();
</script>

@endsection