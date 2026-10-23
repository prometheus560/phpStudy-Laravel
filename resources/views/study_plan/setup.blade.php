@extends('layouts.app')

@section('title', 'Study Plan Setup')

@section('content')
@php
    $hasOld = old('windows') !== null;

    $rows = array_values((array) old('commitments', $prefs['commitments'] ?? []));
    for ($n = 0; $n < 3; $n++) {
        $rows[] = ['day' => '', 'start' => '', 'end' => '', 'label' => ''];
    }

    $sessionNow = (string) old('session_length', $prefs['session'] ?? '50');
    $peakNow = old('peak', $prefs['peak'] ?? 'evening');

    $difficultyLabels = [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'];
@endphp

<div class="px-4 sm:px-8 py-8 max-w-4xl mx-auto">

    <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Plan your study week</h1>
            <p class="page-sub">Tell us how you study and when you are free. We build the weekly plan from this.</p>
        </div>

        @if ($configured)
            <a href="{{ route('study_plan.index') }}" class="btn-ghost">&larr; Back to plan</a>
        @endif
    </div>

    @if ($subjects->isEmpty())
        <div class="alert alert-err">
            You have no subjects yet. Add at least one subject, then come back to set up your plan.
            <a href="{{ route('subjects.index') }}" class="underline font-semibold">Go to Subjects</a>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err">
            <b>Please fix the following:</b>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('study_plan.save') }}" class="space-y-6">
        @csrf

        {{-- Subjects --}}
        <section class="panel">
            <div class="panel-head"><h2>Your subjects</h2></div>

            @forelse ($subjects as $s)
                @php
$f = Join-Path (Get-Location) "resources\views\study_plan\setup.blade.php"
$content = @'
@extends('layouts.app')

@section('title', 'Study Plan Setup')

@section('content')
@php
    $hasOld = old('windows') !== null;

    $rows = array_values((array) old('commitments', $prefs['commitments'] ?? []));
    for ($n = 0; $n < 3; $n++) {
        $rows[] = ['day' => '', 'start' => '', 'end' => '', 'label' => ''];
    }

    $sessionNow = (string) old('session_length', $prefs['session'] ?? '50');
    $peakNow = old('peak', $prefs['peak'] ?? 'evening');

    $difficultyLabels = [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'];
@endphp

<div class="px-4 sm:px-8 py-8 max-w-4xl mx-auto">

    <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="page-title">Plan your study week</h1>
            <p class="page-sub">Tell us how you study and when you are free. We build the weekly plan from this.</p>
        </div>

        @if ($configured)
            <a href="{{ route('study_plan.index') }}" class="btn-ghost">&larr; Back to plan</a>
        @endif
    </div>

    @if ($subjects->isEmpty())
        <div class="alert alert-err">
            You have no subjects yet. Add at least one subject, then come back to set up your plan.
            <a href="{{ route('subjects.index') }}" class="underline font-semibold">Go to Subjects</a>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-err">
            <b>Please fix the following:</b>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('study_plan.save') }}" class="space-y-6">
        @csrf

        {{-- Subjects --}}
        <section class="panel">
            <div class="panel-head"><h2>Your subjects</h2></div>

            @forelse ($subjects as $s)
                @php
                    $exam = old("subjects.{$s->id}.exam_date", $s->exam_date ? \Carbon\Carbon::parse($s->exam_date)->format('Y-m-d') : '');
                @endphp

                <div class="grid gap-4 py-4 sm:grid-cols-4 items-end" style="border-bottom:1px solid var(--line)">
                    <div class="sm:col-span-1">
                        <span class="work-title" style="font-size:16px">{{ $s->subject_name }}</span>
                    </div>

                    <div>
                        <label class="f-label" for="diff-{{ $s->id }}">Difficulty</label>
                        <select id="diff-{{ $s->id }}" name="subjects[{{ $s->id }}][difficulty]" class="f-input">
                            @foreach ($difficultyLabels as $value => $label)
                                <option value="{{ $value }}" @selected((int) old("subjects.{$s->id}.difficulty", $s->difficulty ?? 2) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="f-label" for="credit-{{ $s->id }}">Credit hours</label>
                        <select id="credit-{{ $s->id }}" name="subjects[{{ $s->id }}][credit_hours]" class="f-input">
                            @for ($c = 1; $c <= 6; $c++)
                                <option value="{{ $c }}" @selected((int) old("subjects.{$s->id}.credit_hours", $s->credit_hours ?? 3) === $c)>{{ $c }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="f-label" for="exam-{{ $s->id }}">Exam date (optional)</label>
                        <input id="exam-{{ $s->id }}" type="date" name="subjects[{{ $s->id }}][exam_date]" value="{{ $exam }}" class="f-input">
                    </div>
                </div>
            @empty
                <p class="page-sub" style="padding:16px 0">No subjects to set up yet.</p>
            @endforelse
        </section>

        {{-- Session style --}}
        <section class="panel">
            <div class="panel-head"><h2>How you study</h2></div>

            <div class="grid gap-5 pt-5 sm:grid-cols-2">
                <div>
                    <label class="f-label" for="session_length">Session length</label>
                    <select id="session_length" name="session_length" class="f-input">
                        <option value="25" @selected($sessionNow === '25')>25 minutes (short bursts)</option>
                        <option value="50" @selected($sessionNow === '50')>50 minutes (balanced)</option>
                        <option value="90" @selected($sessionNow === '90')>90 minutes (deep focus)</option>
                    </select>
                </div>

                <div>
                    <label class="f-label" for="peak">When you focus best</label>
                    <select id="peak" name="peak" class="f-input">
                        <option value="morning" @selected($peakNow === 'morning')>Morning</option>
                        <option value="afternoon" @selected($peakNow === 'afternoon')>Afternoon</option>
                        <option value="evening" @selected($peakNow === 'evening')>Evening</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- Study windows --}}
        <section class="panel">
            <div class="panel-head"><h2>Days and times you can study</h2></div>
            <p class="page-sub" style="margin:12px 0 4px">Turn on the days you can study and set the hours for each one.</p>

            @foreach ($days as $key => $name)
                @php
                    $w = $prefs['windows'][$key] ?? [];
                    $on = $hasOld ? !empty(old("windows.$key.on")) : !empty($w['on']);
                    $start = old("windows.$key.start", $w['start'] ?? '17:00');
                    $end = old("windows.$key.end", $w['end'] ?? '21:00');
                @endphp

                <div class="flex flex-wrap items-center gap-4 py-3" style="border-bottom:1px solid var(--line)">
                    <label class="flex items-center gap-3 w-40 cursor-pointer">
                        <input type="checkbox" name="windows[{{ $key }}][on]" value="1" @checked($on) class="w-5 h-5 accent-violet-500">
                        <span class="text-white font-medium">{{ $name }}</span>
                    </label>

                    <input type="time" name="windows[{{ $key }}][start]" value="{{ $start }}" class="f-input" style="width:150px" aria-label="{{ $name }} start time">
                    <span class="text-gray-500">to</span>
                    <input type="time" name="windows[{{ $key }}][end]" value="{{ $end }}" class="f-input" style="width:150px" aria-label="{{ $name }} end time">
                </div>
            @endforeach
        </section>

        {{-- Fixed commitments --}}
        <section class="panel">
            <div class="panel-head"><h2>Fixed weekly commitments</h2></div>
            <p class="page-sub" style="margin:12px 0 4px">Classes, work or anything else we should plan around. Leave unused rows empty.</p>

            @foreach ($rows as $i => $row)
                <div class="grid gap-3 py-3 sm:grid-cols-4" style="border-bottom:1px solid var(--line)">
                    <select name="commitments[{{ $i }}][day]" class="f-input" aria-label="Commitment {{ $i + 1 }} day">
                        <option value="">Day</option>
                        @foreach ($days as $key => $name)
                            <option value="{{ $key }}" @selected(($row['day'] ?? '') === $key)>{{ $name }}</option>
                        @endforeach
                    </select>

                    <input type="time" name="commitments[{{ $i }}][start]" value="{{ $row['start'] ?? '' }}" class="f-input" aria-label="Commitment {{ $i + 1 }} start time">
                    <input type="time" name="commitments[{{ $i }}][end]" value="{{ $row['end'] ?? '' }}" class="f-input" aria-label="Commitment {{ $i + 1 }} end time">
                    <input type="text" name="commitments[{{ $i }}][label]" value="{{ $row['label'] ?? '' }}" maxlength="60" placeholder="What is it? (optional)" class="f-input" aria-label="Commitment {{ $i + 1 }} label">
                </div>
            @endforeach
        </section>

        <div class="flex justify-end gap-3">
            @if ($configured)
                <a href="{{ route('study_plan.index') }}" class="btn-ghost">Cancel</a>
            @endif
            <button type="submit" class="btn-primary" @disabled($subjects->isEmpty())>Save and build my plan</button>
        </div>
    </form>

</div>
@endsection