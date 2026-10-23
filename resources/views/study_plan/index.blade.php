@extends('layouts.app')

@section('title', 'Study Plan')

@section('content')
@php
    $fmt = fn ($t) => date('g:i A', strtotime($t));
    $hours = $plan['summary']['minutes'] / 60;
    $hoursText = rtrim(rtrim(number_format($hours, 1), '0'), '.');
    $diffNames = [3 => 'Hard', 2 => 'Medium', 1 => 'Easy'];
    $diffPill = [3 => 'pg-p-high', 2 => 'pg-p-medium', 1 => 'pg-p-low'];
    $hasFree = collect($plan['days'])->contains(
        fn ($d) => collect($d['items'])->contains(fn ($i) => $i['type'] === 'study' && ($i['state'] ?? '') === 'free' && empty($i['late']))
    );
@endphp

@include('partials.page-kit')
@include('partials.study-plan-kit')

<div class="pg">

    <header class="pg-head">
        <div style="position:relative;z-index:1">
            <h1 class="pg-title">Study Plan</h1>
            <p class="pg-sub">{{ $start->format('M d') }} to {{ $end->format('M d, Y') }} &middot; {{ $plan['block']['label'] }}</p>
        </div>

        <div class="pg-actions">
            <a href="{{ route('study_plan.today') }}" class="pg-btn ghost">Today</a>
            <a href="{{ route('study_plan.setup') }}" class="pg-btn ghost">Setup</a>

            <form method="POST" action="{{ route('study_plan.schedule') }}">
                @csrf
                <input type="hidden" name="w" value="{{ $week }}">
                <button type="submit" class="pg-btn main" @disabled(!$hasFree)>Add to my schedule</button>
            </form>
        </div>
    </header>

    @if (session('success'))<div class="pg-flash" role="status">{{ session('success') }}</div>@endif
    @if (session('info'))<div class="pg-flash info" role="status">{{ session('info') }}</div>@endif

    @foreach ($plan['warnings'] as $warning)
        <div class="sp-note" role="alert">{{ $warning }}</div>
    @endforeach

    <div class="sp-tabs">
        <a href="{{ route('study_plan.index', ['w' => 0]) }}" class="pg-chip {{ $week === 0 ? 'on' : '' }}">This week</a>
        <a href="{{ route('study_plan.index', ['w' => 1]) }}" class="pg-chip {{ $week === 1 ? 'on' : '' }}">Next week</a>
        <a href="{{ route('study_plan.index', ['w' => 2]) }}" class="pg-chip {{ $week === 2 ? 'on' : '' }}">In 2 weeks</a>
    </div>

    <div class="pg-stats">
        <div class="pg-stat" style="--c:#60a5fa"><b>{{ $hoursText }}</b><span>Study hours</span></div>
        <div class="pg-stat" style="--c:#4ade80"><b>{{ $plan['summary']['sessions'] }}</b><span>Study blocks</span></div>
        <div class="pg-stat" style="--c:#a78bfa"><b>{{ $plan['summary']['buffer'] }}</b><span>Buffer blocks</span></div>
    </div>

    <div class="pg-section">
        <h3>Weekly timetable</h3>
        <small>Hard subjects sit in your peak hours and repeat through the week</small>
    </div>

    <div class="sp-week">
        @foreach ($plan['days'] as $day)
            @php
                $dayHours = rtrim(rtrim(number_format($day['minutes'] / 60, 1), '0'), '.');
            @endphp

            <section class="sp-day {{ $day['today'] ? 'today' : '' }} {{ $day['past'] ? 'past' : '' }}">
                <div class="sp-dayhead">
                    <span class="sp-dayname">{{ $day['name'] }}</span>
                    <span class="sp-daydate">{{ $day['date']->format('M d') }}</span>
                </div>

                <div class="sp-daysum">
                    @if ($day['today']) <span class="sp-tag now">Today</span> @endif
                    @if ($day['buffer_day']) <span class="sp-tag buf">Buffer day</span> @endif
                    @if ($day['minutes'] > 0) {{ $dayHours }} h of study @endif
                </div>

                @if (count($day['events']) > 0)
                    <div class="sp-events">
                        @foreach ($day['events'] as $event)
                            <span class="sp-ev {{ $event['kind'] }}">{{ $event['text'] }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="sp-items">
                    @forelse ($day['items'] as $item)
                        @if ($item['type'] === 'busy')

                            <div class="sp-item busy">
                                <span class="sp-time">{{ $fmt($item['start']) }} - {{ $fmt($item['end']) }}</span>
                                <span class="sp-what" style="font-weight:500">{{ $item['label'] }}</span>
                            </div>

                        @elseif ($item['type'] === 'buffer')

                            <div class="sp-item buffer {{ !empty($item['late']) ? 'late' : '' }}">
                                <span class="sp-time">{{ $fmt($item['start']) }} - {{ $fmt($item['end']) }}</span>
                                <span class="sp-what">Buffer: catch up</span>
                                <p class="sp-how">Finish anything you missed this week. If you are on track, do a light review or rest.</p>
                            </div>

                        @elseif ($item['type'] === 'free')

                            <div class="sp-item free">
                                <span class="sp-time">{{ $fmt($item['start']) }} - {{ $fmt($item['end']) }}</span>
                                <span class="sp-what" style="font-weight:500">Open time</span>
                            </div>

                        @else

                            <div class="sp-item d{{ $item['difficulty'] }} {{ !empty($item['late']) ? 'late' : '' }}">
                                <span class="sp-time">{{ $fmt($item['start']) }} - {{ $fmt($item['end']) }}</span>
                                <span class="sp-what">{{ $item['subject'] }}</span>

                                @if ($item['task'])
                                    <p class="sp-how">Task: {{ $item['task'] }}@if ($item['task_due']) (due {{ $item['task_due'] }})@endif</p>
                                @else
                                    <p class="sp-how"><b style="font-weight:600;color:#e4e6f2">{{ $item['activity'] }}.</b> {{ $item['detail'] }}</p>
                                @endif

                                <div class="sp-foot">
                                    <span>{{ $item['focus_minutes'] }} min focus</span>
                                    @if (($item['state'] ?? '') === 'saved')
                                        <span class="sp-tag ok">In schedule</span>
                                    @elseif (($item['state'] ?? '') === 'clash')
                                        <span class="sp-tag warn">Overlaps a planned session</span>
                                    @endif
                                </div>
                            </div>

                        @endif
                    @empty
                        <p class="sp-empty-day">
                            {{ $day['past'] ? 'Past day.' : 'No study time on this day. Rest, or turn it on in Setup.' }}
                        </p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <div class="pg-section" style="margin-top:28px">
        <h3>How to study each subject</h3>
        <small>Matched to difficulty</small>
    </div>

    <div class="sp-grid">
        @foreach ($plan['subjects'] as $s)
            @php
                $list = \App\Services\StudyPlanner::strategies($s['diff']);
                $left = $s['days_to_exam'];
            @endphp

            <article class="sp-card">
                <h4>{{ $s['name'] }}</h4>

                <div class="sp-meta">
                    <span class="pg-pill {{ $diffPill[$s['diff']] }}">{{ $diffNames[$s['diff']] }}</span>
                    <span class="pg-pill">{{ $s['sessions'] }} {{ $s['sessions'] === 1 ? 'block' : 'blocks' }} this week</span>
                    @if ($left !== null)
                        <span class="pg-pill {{ $left <= 7 ? 'pg-p-high' : '' }}">
                            Exam {{ $left === 0 ? 'today' : ($left === 1 ? 'tomorrow' : 'in ' . $left . ' days') }}
                        </span>
                    @endif
                </div>

                @foreach (array_slice($list, 0, 2) as $tech)
                    <p class="sp-tech"><b>{{ $tech['name'] }}</b>{{ $tech['how'] }}</p>
                @endforeach

                @if (count($list) > 2)
                    <details style="margin-top:12px">
                        <summary>More techniques</summary>
                        @foreach (array_slice($list, 2) as $tech)
                            <p class="sp-tech"><b>{{ $tech['name'] }}</b>{{ $tech['how'] }} <span style="color:#8d92ad">{{ $tech['why'] }}</span></p>
                        @endforeach
                    </details>
                @endif
            </article>
        @endforeach
    </div>

    <details class="pg-panel pad sp-how-panel" style="margin-top:28px">
        <summary>How this plan is built</summary>
        <ul>
            <li>Spaced repetition: hard subjects come back several times a week instead of one long block.</li>
            <li>Interleaving: neighbouring blocks in a day are different subjects.</li>
            <li>Energy matching: the hardest subjects get the blocks closest to your peak time.</li>
            <li>Buffer: the last study day keeps catch-up blocks for missed work.</li>
            <li>Exams and deadlines raise a subject's share of the week. Pending tasks are placed into that subject's blocks, earliest deadline first.</li>
            <li>Low on energy? Open <a href="{{ route('study_plan.today', ['mode' => 'lite']) }}" style="color:#7db4ff">the low-energy day</a>.</li>
        </ul>
    </details>

</div>
@endsection