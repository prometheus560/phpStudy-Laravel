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
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">Study Schedule</h2>
            <p class="text-gray-400 text-sm mt-1">Plan your study time and stay organized.</p>
        </div>
        <span class="text-xs font-semibold bg-blue-500/10 text-blue-300 border border-blue-500/20 rounded-lg px-3 py-1.5">
            {{ $upcomingCount }} upcoming {{ $upcomingCount == 1 ? 'session' : 'sessions' }}
        </span>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    {{-- Add Schedule --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white">Add Study Session</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Pick a subject, a date, and the time you want to study.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">{{ $errors->first() }}</div>
        @endif

        @if ($subjects->count() > 0)

            <form method="POST" action="{{ route('schedule.store') }}" id="schedule-form" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @csrf

                <div class="sm:col-span-3">
                    <label for="subject_id" class="block text-sm font-semibold text-gray-200 mb-1.5">Subject</label>
                    <select id="subject_id" name="subject_id" required class="{{ $field }}">
                        <option value="">Select subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="study_date" class="block text-sm font-semibold text-gray-200 mb-1.5">Study date</label>
                    <input id="study_date" type="date" name="study_date" value="{{ old('study_date') }}" required class="{{ $field }}">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-semibold text-gray-200 mb-1.5">Start time</label>
                    <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required class="{{ $field }}">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-semibold text-gray-200 mb-1.5">End time</label>
                    <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required class="{{ $field }}">
                </div>

                <p id="time-error" class="hidden sm:col-span-3 text-sm text-red-400">End time must be later than the start time.</p>

                <div class="sm:col-span-3">
                    <button type="submit"
                            class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                        + Add Session
                    </button>
                </div>
            </form>

        @else

            <div class="text-center py-8">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-gray-400 text-sm mt-1">You need to create a subject before creating a schedule.</p>
                <a href="{{ route('subjects.index') }}" class="inline-block mt-4 bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500">Add Subject</a>
            </div>

        @endif
    </div>

    {{-- Schedule List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">My Schedule</h3>
            <p class="text-gray-400 text-sm mt-1">{{ $schedules->count() }} {{ $schedules->count() == 1 ? 'study session' : 'study sessions' }} planned.</p>
        </div>

        @if ($schedules->count() > 0)

            @foreach ($sections as $kind => $set)
                @if ($set->count() > 0)

                    @if ($kind === 'Past')
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-8 mb-3">Past sessions</p>
                    @endif

                    @foreach ($set as $date => $items)
                        @php
                            $d    = \Carbon\Carbon::parse($date)->startOfDay();
                            $diff = (int) today()->diffInDays($d, false);
                            $tag  = $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : null);
                        @endphp

                        <div class="mb-6 last:mb-0 {{ $kind === 'Past' ? 'opacity-60' : '' }}">
                            <div class="flex items-center gap-3 mb-3">
                                <h4 class="text-sm font-semibold text-gray-200">{{ $d->format('l, F d, Y') }}</h4>
                                @if ($tag)
                                    <span class="text-[11px] font-bold rounded-md px-2 py-0.5 {{ $diff === 0 ? 'bg-blue-500/15 text-blue-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $tag }}</span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-3">
                                @foreach ($items as $schedule)
                                    @php
                                        $start = strtotime($schedule->start_time);
                                        $end   = strtotime($schedule->end_time);
                                        $mins  = $end > $start ? (int) (($end - $start) / 60) : 0;
                                        $dur   = $mins >= 60 ? intdiv($mins, 60) . 'h' . ($mins % 60 ? ' ' . ($mins % 60) . 'm' : '') : $mins . 'm';
                                    @endphp

                                    <div class="flex flex-wrap items-center gap-4 rounded-xl border border-[#23232f] bg-[#1b1b28] p-4">
                                        <div class="w-28 shrink-0">
                                            <p class="text-white font-semibold">{{ date('g:i A', $start) }}</p>
                                            <p class="text-xs text-gray-500">to {{ date('g:i A', $end) }}</p>
                                        </div>

                                        <div class="flex-1 min-w-[160px]">
                                            <p class="text-blue-300 font-semibold">{{ $schedule->subject->subject_name }}</p>
                                            @if ($mins > 0)
                                                <span class="inline-block mt-1 text-[11px] font-semibold bg-white/5 text-gray-300 rounded px-2 py-0.5">{{ $dur }}</span>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('schedule.destroy', $schedule) }}"
                                              onsubmit="return confirm('Delete this study session?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-3.5 py-2 text-sm font-medium border border-red-500/30 text-red-400 hover:bg-red-500/10">Delete</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                @endif
            @endforeach

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No study sessions yet</h4>
                <p class="text-gray-400 text-sm mt-1">Add your first session above to start planning your time.</p>
            </div>

        @endif
    </div>
</div>

<script>
    const sForm = document.getElementById('schedule-form');
    if (sForm) {
        sForm.addEventListener('submit', e => {
            const s = document.getElementById('start_time').value;
            const t = document.getElementById('end_time').value;
            const bad = s && t && t <= s;
            document.getElementById('time-error').classList.toggle('hidden', !bad);
            if (bad) e.preventDefault();
        });
    }
</script>

@endsection