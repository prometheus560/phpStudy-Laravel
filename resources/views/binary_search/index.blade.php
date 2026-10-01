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
    </div>

    {{-- Subject list --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">
        <h3 class="text-lg font-bold text-white mb-1">Your Subjects</h3>
        <p class="text-gray-400 text-sm mb-4">Subjects available for searching.</p>

        @if ($subjects->count() > 0)

            <div class="flex flex-col divide-y divide-[#23232f]">
                @foreach ($subjects as $subject)
                    <div class="py-3.5 subject-row transition" data-name="{{ mb_strtolower($subject->subject_name) }}">
                        <strong class="text-white">{{ $subject->subject_name }}</strong>
                    </div>
                @endforeach
            </div>

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

{{-- Subject names sorted A-Z (required for binary search) --}}
@php
    $sortedNames = $subjects
        ->pluck('subject_name')
        ->sortBy(fn ($n) => mb_strtolower($n))
        ->values();
@endphp

<script>
(() => {
    const input = document.getElementById('subject-search');
    const box = document.getElementById('subject-results');
    if (!input) return;

    // Sorted subject names
    const sorted = @json($sortedNames).map(name => ({ name, key: name.toLowerCase() }));

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

    input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        box.innerHTML = '';

        if (!q) { box.classList.add('hidden'); return; }

        const results = search(q);

        if (results.length === 0) {
            const li = document.createElement('li');
            li.className = 'px-4 py-2 text-sm text-gray-500';
            li.textContent = 'No subjects found';
            box.appendChild(li);
        } else {
            results.forEach(s => {
                const li = document.createElement('li');
                li.className = 'px-4 py-2 text-sm text-gray-200 hover:bg-[#23232f] cursor-pointer';
                li.textContent = s.name;
                li.addEventListener('click', () => {
                    input.value = s.name;
                    box.classList.add('hidden');
                    highlight(s.key);
                });
                box.appendChild(li);
            });
        }

        box.classList.remove('hidden');
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