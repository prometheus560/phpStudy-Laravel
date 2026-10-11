@extends('layouts.app')

@section('title', 'Study Schedule')

@section('content')

@php
    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-2.5 text-sm transition';

    $today  = today()->format('Y-m-d');
    $sorted = $schedules->sortBy(fn ($s) => $s->study_date->format('Y-m-d') . ' ' . $s->start_time)->values();
    $groups = $sorted->groupBy(fn ($s) => $s->study_date->format('Y-m-d'));

    $sections = [
        'Upcoming' => $groups->filter(fn ($g, $k) => $k >= $today),
        'Past'     => $groups->filter(fn ($g, $k) => $k < $today)->reverse(),
    ];

    $upcomingCount = $sections['Upcoming']->sum(fn ($g) => $g->count());
    $pastCount     = $sections['Past']->sum(fn ($g) => $g->count());

    $minsOf = fn ($s) => max(0, (int) ((strtotime($s->end_time) - strtotime($s->start_time)) / 60));

    // Hours planned this week
    $weekStart = today()->startOfWeek()->format('Y-m-d');
    $weekEnd   = today()->endOfWeek()->format('Y-m-d');
    $weekMins  = $sorted
        ->filter(fn ($s) => $s->study_date->format('Y-m-d') >= $weekStart && $s->study_date->format('Y-m-d') <= $weekEnd)
        ->sum(fn ($s) => $minsOf($s));
    $weekHours = rtrim(rtrim(number_format($weekMins / 60, 1), '0'), '.');

    // Next session that has not started yet
    $next = $sorted->first(fn ($s) => \Carbon\Carbon::parse($s->study_date->format('Y-m-d') . ' ' . $s->start_time)->gte(now()));

    $durText = function ($mins) {
        return $mins >= 60 ? intdiv($mins, 60) . 'h' . ($mins % 60 ? ' ' . ($mins % 60) . 'm' : '') : $mins . 'm';
    };
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Study Schedule</h2>
            <p class="text-gray-400 text-sm mt-1">Plan your study time and stay organized.</p>
        </div>

        @if ($subjects->count() > 0)
            <button type="button" data-open-add
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Session
            </button>
        @else
            <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">Add Subject first</a>
        @endif
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
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $upcomingCount }}</p>
                <p class="text-gray-400 text-sm mt-1">Upcoming {{ $upcomingCount == 1 ? 'session' : 'sessions' }}</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-orange-500/15 border border-orange-500/20 text-orange-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
                <p class="text-2xl font-bold text-white leading-none">{{ $weekHours === '' ? '0' : $weekHours }}h</p>
                <p class="text-gray-400 text-sm mt-1">Planned this week</p>
            </div>
        </div>

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-5 flex items-center gap-4">
            <span class="w-11 h-11 rounded-xl bg-green-500/15 border border-green-500/20 text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </span>
            <div class="min-w-0">
                @if ($next)
                    <p class="text-base font-bold text-white leading-tight truncate">{{ $next->subject->subject_name }}</p>
                    <p class="text-gray-400 text-sm mt-1">{{ $next->study_date->format('M d') }} &middot; {{ date('g:i A', strtotime($next->start_time)) }}</p>
                @else
                    <p class="text-base font-bold text-white leading-tight">None planned</p>
                    <p class="text-gray-400 text-sm mt-1">Next session</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Schedule list --}}
    <div class="mb-5">
        <h3 class="text-lg font-bold text-white">My Schedule</h3>
        <p class="text-gray-500 text-sm mt-1">{{ $schedules->count() }} {{ $schedules->count() == 1 ? 'study session' : 'study sessions' }} planned.</p>
    </div>

    @if ($schedules->count() > 0)

        @foreach ($sections as $kind => $set)
            @if ($set->count() > 0)

                @if ($kind === 'Past')
                    <details class="mt-8">
                        <summary class="cursor-pointer text-xs font-semibold uppercase tracking-wider text-gray-500 hover:text-gray-300 mb-4">
                            Past sessions ({{ $pastCount }})
                        </summary>
                @endif

                @foreach ($set as $date => $items)
                    @php
                        $d    = \Carbon\Carbon::parse($date)->startOfDay();
                        $diff = (int) today()->diffInDays($d, false);
                        $tag  = $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : null);
                    @endphp

                    <div class="mb-6 {{ $kind === 'Past' ? 'opacity-60' : '' }}">
                        <div class="flex items-center gap-3 mb-3">
                            <h4 class="text-sm font-semibold text-gray-200">{{ $d->format('l, F d, Y') }}</h4>
                            @if ($tag)
                                <span class="text-[11px] font-bold rounded-md px-2 py-0.5 {{ $diff === 0 ? 'bg-blue-500/15 text-blue-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $tag }}</span>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3">
                            @foreach ($items as $schedule)
                                @php
                                    $mins = $minsOf($schedule);
                                    $s24  = substr($schedule->start_time, 0, 5);
                                    $e24  = substr($schedule->end_time, 0, 5);
                                @endphp

                                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-white/[.08] border-l-4 {{ $diff === 0 ? 'border-l-blue-500' : ($kind === 'Past' ? 'border-l-gray-600' : 'border-l-indigo-500/60') }} bg-white/[.03] hover:border-blue-500/30 transition p-4">
                                    <div class="w-28 shrink-0">
                                        <p class="text-white font-semibold">{{ date('g:i A', strtotime($schedule->start_time)) }}</p>
                                        <p class="text-xs text-gray-500">to {{ date('g:i A', strtotime($schedule->end_time)) }}</p>
                                    </div>

                                    <div class="flex-1 min-w-[160px]">
                                        <p class="text-blue-300 font-semibold">{{ $schedule->subject->subject_name }}</p>
                                        @if ($mins > 0)
                                            <span class="inline-block mt-1 text-[11px] font-semibold bg-white/5 text-gray-300 rounded px-2 py-0.5">{{ $durText($mins) }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="edit-btn rounded-lg px-3.5 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:text-white hover:bg-white/5"
                                                data-session="{{ json_encode([
                                                    'id'         => $schedule->id,
                                                    'subject_id' => $schedule->subject_id,
                                                    'date'       => $schedule->study_date->format('Y-m-d'),
                                                    'start'      => $s24,
                                                    'end'        => $e24,
                                                ]) }}">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('schedule.destroy', $schedule) }}"
                                              onsubmit="return confirm('Delete this study session?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-3.5 py-2 text-sm font-medium border border-red-500/30 text-red-400 hover:bg-red-500/10">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($kind === 'Past')
                    </details>
                @endif

            @endif
        @endforeach

    @else

        <div class="bg-[#14141f] border border-[#23232f] rounded-2xl text-center py-16 px-6">
            <span class="w-14 h-14 mx-auto rounded-2xl bg-blue-600/15 border border-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>

            @if ($subjects->count() > 0)
                <h4 class="font-semibold text-white">No study sessions yet</h4>
                <p class="text-gray-400 text-sm mt-1 mb-5">Add your first session to start planning your time.</p>
                <button type="button" data-open-add class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">+ Add Session</button>
            @else
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-gray-400 text-sm mt-1 mb-5">You need to create a subject before creating a schedule.</p>
                <a href="{{ route('subjects.index') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold">Add Subject</a>
            @endif
        </div>

    @endif

</div>

@if ($subjects->count() > 0)

{{-- Add dialog --}}
<dialog id="add-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" action="{{ route('schedule.store') }}" class="time-form bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-200">
        @csrf

        <div class="sm:col-span-2">
            <h3 class="text-lg font-bold text-white">Add study session</h3>
            <p class="text-gray-400 text-sm mt-1">Pick a subject, a date, and the time you want to study.</p>
        </div>

        <div class="sm:col-span-2">
            <label for="subject_id" class="block text-sm font-semibold mb-1.5">Subject</label>
            <select id="subject_id" name="subject_id" required class="{{ $field }}">
                <option value="">Select subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label for="study_date" class="block text-sm font-semibold mb-1.5">Study date</label>
            <input id="study_date" type="date" name="study_date" value="{{ old('study_date') }}" required class="{{ $field }}">
        </div>

        <div>
            <label for="start_time" class="block text-sm font-semibold mb-1.5">Start time</label>
            <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required class="{{ $field }}">
        </div>

        <div>
            <label for="end_time" class="block text-sm font-semibold mb-1.5">End time</label>
            <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required class="{{ $field }}">
        </div>

        <p class="time-error hidden sm:col-span-2 text-sm text-red-400">End time must be later than the start time.</p>

        <div class="sm:col-span-2 flex justify-end gap-2 pt-1">
            <button type="button" id="add-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">+ Add Session</button>
        </div>
    </form>
</dialog>

{{-- Edit dialog --}}
<dialog id="edit-dialog" class="bg-transparent p-0 m-auto w-full max-w-lg backdrop:bg-black/70">
    <form method="POST" id="edit-form" class="time-form bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-200">
        @csrf
        @method('PATCH')

        <div class="sm:col-span-2">
            <h3 class="text-lg font-bold text-white">Edit study session</h3>
        </div>

        <div class="sm:col-span-2">
            <label for="e_subject" class="block text-sm font-semibold mb-1.5">Subject</label>
            <select id="e_subject" name="subject_id" required class="{{ $field }}">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label for="e_date" class="block text-sm font-semibold mb-1.5">Study date</label>
            <input id="e_date" type="date" name="study_date" required class="{{ $field }}">
        </div>

        <div>
            <label for="e_start" class="block text-sm font-semibold mb-1.5">Start time</label>
            <input id="e_start" type="time" name="start_time" required class="{{ $field }}">
        </div>

        <div>
            <label for="e_end" class="block text-sm font-semibold mb-1.5">End time</label>
            <input id="e_end" type="time" name="end_time" required class="{{ $field }}">
        </div>

        <p class="time-error hidden sm:col-span-2 text-sm text-red-400">End time must be later than the start time.</p>

        <div class="sm:col-span-2 flex justify-end gap-2 pt-1">
            <button type="button" id="edit-cancel" class="rounded-xl px-4 py-2 text-sm font-medium border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
            <button type="submit" class="rounded-xl px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500">Save changes</button>
        </div>
    </form>
</dialog>

<script>
(() => {
    const add  = document.getElementById('add-dialog');
    const dlg  = document.getElementById('edit-dialog');
    const form = document.getElementById('edit-form');

    // Add dialog
    document.querySelectorAll('[data-open-add]').forEach(b => b.addEventListener('click', () => add.showModal()));
    document.getElementById('add-cancel').addEventListener('click', () => add.close());
    add.addEventListener('click', e => { if (e.target === add) add.close(); });

    // Edit dialog
    document.querySelectorAll('.edit-btn').forEach(b => b.addEventListener('click', () => {
        const s = JSON.parse(b.dataset.session);
        form.action = "{{ url('/schedule') }}/" + s.id;
        document.getElementById('e_subject').value = s.subject_id;
        document.getElementById('e_date').value    = s.date;
        document.getElementById('e_start').value   = s.start;
        document.getElementById('e_end').value     = s.end;
        form.querySelector('.time-error').classList.add('hidden');
        dlg.showModal();
    }));
    document.getElementById('edit-cancel').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

    // End time must be later than start time (both forms)
    document.querySelectorAll('.time-form').forEach(f => f.addEventListener('submit', e => {
        const s = f.querySelector('[name="start_time"]').value;
        const t = f.querySelector('[name="end_time"]').value;
        const bad = s && t && t <= s;
        f.querySelector('.time-error').classList.toggle('hidden', !bad);

        if (bad) { e.preventDefault(); return; }

        // Stop double clicks from sending the form twice
        const btn = f.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    }));

    // Remember button labels and restore them if the user comes back with the Back button
    document.querySelectorAll('.time-form button[type="submit"]').forEach(b => { b.dataset.label = b.textContent.trim(); });
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('.time-form button[type="submit"]').forEach(b => {
            b.disabled = false;
            b.textContent = b.dataset.label;
        });
    });
})();
</script>

@endif

@endsection