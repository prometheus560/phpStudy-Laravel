<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds a weekly study timetable.
 *
 * Principles used:
 *  - Spaced repetition: a subject is picked more often when it has gone unstudied for a few days,
 *    and no subject is stacked into one long block (per-day cap).
 *  - Interleaving: neighbouring blocks in a day are different subjects whenever possible.
 *  - Energy matching: the hardest subjects get the blocks closest to the student's peak time.
 *  - Buffer: the last study day of the week keeps catch-up blocks (or one block when few days are free).
 *
 * Nothing here touches the database. The controller passes data in and gets a plain array back.
 */
class StudyPlanner
{
    public const DAYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    public const DAY_NAMES = [
        'mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday',
        'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday',
    ];

    // focus = minutes of focus, rest = short break inside a block, cycles = focus rounds per block,
    // gap = break between two blocks
    public const SESSIONS = [
        '25' => ['focus' => 25, 'rest' => 5,  'cycles' => 2, 'gap' => 10, 'label' => 'Pomodoro 25/5'],
        '50' => ['focus' => 50, 'rest' => 10, 'cycles' => 1, 'gap' => 10, 'label' => '50 min focus, 10 min break'],
        '90' => ['focus' => 90, 'rest' => 15, 'cycles' => 1, 'gap' => 15, 'label' => 'Deep work 90/15'],
    ];

    public const DIFFICULTY = [1 => 'Easy', 2 => 'Medium', 3 => 'Hard'];

    public static function defaults(): array
    {
        $windows = [];

        foreach (self::DAYS as $day) {
            $weekend = in_array($day, ['sat', 'sun'], true);

            $windows[$day] = [
                'on'    => true,
                'start' => $weekend ? '10:00' : '17:00',
                'end'   => $weekend ? '16:00' : '21:00',
            ];
        }

        return [
            'session'     => '50',
            'peak'        => 'evening',
            'windows'     => $windows,
            'commitments' => [],
        ];
    }

    public static function toMin(?string $time): int
    {
        $parts = explode(':', (string) $time);

        return ((int) ($parts[0] ?? 0)) * 60 + (int) ($parts[1] ?? 0);
    }

    public static function fromMin(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Study techniques matched to how hard the subject is.
     */
    public static function strategies(int $difficulty): array
    {
        $library = [
            3 => [
                ['name' => 'Practice problems', 'how' => 'Study one worked example, then solve similar problems without looking. Check your answers and write down every mistake.', 'why' => 'Solving problems builds skill that re-reading does not.'],
                ['name' => 'Active recall', 'how' => 'Close your notes. Write or say everything you remember about the topic, then check what you missed.', 'why' => 'Pulling facts out of memory strengthens them more than re-reading.'],
                ['name' => 'Error log review', 'how' => 'Redo the problems you got wrong last time and note the reason behind each mistake.', 'why' => 'Fixing known weak spots is the fastest way to improve.'],
                ['name' => 'Practice test', 'how' => 'Answer past-paper style questions under time pressure, then mark them honestly.', 'why' => 'Practice testing shows what you really know and prepares you for exam pressure.'],
                ['name' => 'Teach it aloud', 'how' => 'Explain the topic out loud as if teaching a friend. Where you stumble is what to study next.', 'why' => 'Explaining exposes gaps in your understanding.'],
            ],
            2 => [
                ['name' => 'Flashcard recall', 'how' => 'Go through your flashcards. Sort them into "knew it" and "missed it", then repeat only the missed pile.', 'why' => 'Repeated retrieval with feedback sticks better than rereading.'],
                ['name' => 'Summarize from memory', 'how' => 'Write a one-page summary of the chapter without looking, then fill in gaps from your notes.', 'why' => 'Recalling the structure of a topic helps you connect ideas.'],
                ['name' => 'Practice questions', 'how' => 'Answer end-of-chapter or past exam questions, then check the answers.', 'why' => 'Testing yourself tells you what to study next.'],
                ['name' => 'Practice test', 'how' => 'Take a short timed quiz on everything covered so far, then mark it.', 'why' => 'Timed practice builds exam confidence.'],
            ],
            1 => [
                ['name' => 'Skim and outline', 'how' => 'Skim the chapter and write a short outline of the main points in your own words.', 'why' => 'A quick outline keeps easy subjects fresh without long sessions.'],
                ['name' => 'Quick flashcards', 'how' => 'Review 15 to 20 flashcards, then quiz yourself on the ones you missed.', 'why' => 'Short retrieval rounds are enough for lighter material.'],
                ['name' => 'Practice test', 'how' => 'Do a short self-quiz on this unit and mark it.', 'why' => 'A quick check confirms what you already know.'],
            ],
        ];

        return $library[$difficulty] ?? $library[2];
    }

    /**
     * Build the timetable for the 7 days that start at $weekStart.
     *
     * @param Collection $subjects  Subject models (difficulty, credit_hours, exam_date may be null)
     * @param array      $prefs     ['session', 'peak', 'windows', 'commitments']
     * @param Collection $tasks     Pending Task models
     */
    public function generate(Collection $subjects, array $prefs, Collection $tasks, Carbon $weekStart): array
    {
        $prefs = array_replace_recursive(self::defaults(), $prefs);
        $cfg = self::SESSIONS[(string) $prefs['session']] ?? self::SESSIONS['50'];

        $focusMin = $cfg['cycles'] * $cfg['focus'];
        $blockLen = $focusMin + ($cfg['cycles'] - 1) * $cfg['rest'];
        $gap = $cfg['gap'];

        $today = Carbon::today();
        $nowMin = ((int) now()->format('G')) * 60 + (int) now()->format('i');
        $weekStart = $weekStart->copy()->startOfDay();
        $warnings = [];

        // ---------- 1. Days and free slots ----------
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $key = self::DAYS[$date->dayOfWeekIso - 1];
            $win = $prefs['windows'][$key];
            $past = $date->lt($today);

            $busy = [];

            foreach ($prefs['commitments'] as $c) {
                if (($c['day'] ?? '') !== $key || empty($c['start']) || empty($c['end'])) {
                    continue;
                }

                $label = trim((string) ($c['label'] ?? ''));
                $busy[] = [self::toMin($c['start']), self::toMin($c['end']), $label !== '' ? $label : 'Busy'];
            }

            usort($busy, fn ($a, $b) => $a[0] <=> $b[0]);

            $slots = [];

            if (!$past && !empty($win['on'])) {
                $free = $this->freeIntervals(self::toMin($win['start']), self::toMin($win['end']), $busy);

                foreach ($free as [$a, $b]) {
                    for ($t = $a; $t + $blockLen <= $b; $t += $blockLen + $gap) {
                        $slots[] = [
                            'type'      => 'study',
                            'start_min' => $t,
                            'end_min'   => $t + $blockLen,
                            'start'     => self::fromMin($t),
                            'end'       => self::fromMin($t + $blockLen),
                        ];
                    }
                }
            }

            $days[] = [
                'date'       => $date,
                'key'        => $key,
                'name'       => self::DAY_NAMES[$key],
                'past'       => $past,
                'today'      => $date->isSameDay($today),
                'busy'       => $busy,
                'slots'      => $slots,
                'buffer_day' => false,
                'events'     => [],
                'items'      => [],
                'minutes'    => 0,
            ];
        }

        if (!collect($prefs['windows'])->contains(fn ($w) => !empty($w['on']))) {
            $warnings[] = 'No study days are turned on. Open Setup and choose when you can study.';
        }

        // ---------- 2. Buffer blocks ----------
        $studyDays = [];

        foreach ($days as $di => $day) {
            if (count($day['slots']) > 0) {
                $studyDays[] = $di;
            }
        }

        if (count($studyDays) >= 4) {
            $bd = end($studyDays);
            $n = count($days[$bd]['slots']);
            $keep = (int) floor($n / 2);              // the first half stays normal study time

            $days[$bd]['buffer_day'] = true;

            for ($k = $keep; $k < $n; $k++) {
                $days[$bd]['slots'][$k]['type'] = 'buffer';
            }
        } elseif (count($studyDays) >= 1) {
            $bd = end($studyDays);
            $last = count($days[$bd]['slots']) - 1;
            $days[$bd]['slots'][$last]['type'] = 'buffer';
        }

        // ---------- 3. Subjects and weights ----------
        $subs = [];
        $weekEnd = $weekStart->copy()->addDays(7);

        foreach ($subjects as $s) {
            $diff = (int) ($s->difficulty ?? 2);
            $diff = ($diff >= 1 && $diff <= 3) ? $diff : 2;

            $credit = (int) ($s->credit_hours ?? 3);
            $credit = ($credit >= 1 && $credit <= 6) ? $credit : 3;

            $exam = $s->exam_date ? Carbon::parse($s->exam_date)->startOfDay() : null;

            if ($exam && $exam->lt($today)) {
                $exam = null;                          // exam already happened, plan as normal
            }

            $daysToExam = $exam ? (int) $today->diffInDays($exam, false) : null;

            $urgency = 1.0;

            if ($daysToExam !== null) {
                if ($daysToExam <= 3) {
                    $urgency = 2.5;
                } elseif ($daysToExam <= 7) {
                    $urgency = 2.0;
                } elseif ($daysToExam <= 14) {
                    $urgency = 1.5;
                } elseif ($daysToExam <= 30) {
                    $urgency = 1.2;
                }
            }

            $queue = $tasks
                ->filter(fn ($t) => (int) $t->subject_id === (int) $s->id)
                ->sortBy(fn ($t) => $t->deadline ? $t->deadline->timestamp : PHP_INT_MAX)
                ->values();

            $soon = $queue->filter(fn ($t) => $t->deadline && $t->deadline->lte($weekEnd))->count();
            $boost = min(1.0, $soon * 0.25);

            $weight = max(0.5, $diff + 0.25 * ($credit - 3)) * $urgency + $boost;

            $subs[$s->id] = [
                'id'           => $s->id,
                'name'         => $s->subject_name,
                'diff'         => $diff,
                'credit'       => $credit,
                'exam'         => $exam,
                'days_to_exam' => $daysToExam,
                'w'            => $weight,
                'target'       => 0,
                'given'        => 0,
                'last'         => null,
                'strat_n'      => 0,
                'queue'        => $queue->all(),
                'sessions'     => 0,
                'minutes'      => 0,
            ];
        }

        if (count($subs) === 0) {
            $warnings[] = 'Add at least one subject to build a plan.';
        }

        // ---------- 4. Weekly targets per subject (largest remainder) ----------
        $normalCount = 0;

        foreach ($days as $day) {
            foreach ($day['slots'] as $slot) {
                if ($slot['type'] === 'study') {
                    $normalCount++;
                }
            }
        }

        if (count($subs) > 0 && $normalCount > 0) {
            $sumW = array_sum(array_column($subs, 'w'));
            $raw = [];

            foreach ($subs as $id => $s) {
                $raw[$id] = $normalCount * $s['w'] / $sumW;
            }

            $target = array_map(fn ($v) => (int) floor($v), $raw);
            $left = $normalCount - array_sum($target);

            $frac = [];

            foreach ($raw as $id => $v) {
                $frac[$id] = $v - floor($v);
            }

            arsort($frac);

            foreach (array_keys($frac) as $id) {
                if ($left <= 0) {
                    break;
                }

                $target[$id]++;
                $left--;
            }

            if ($normalCount >= count($subs)) {
                foreach (array_keys($target) as $id) {
                    if ($target[$id] === 0) {
                        $donor = array_search(max($target), $target, true);
                        $target[$donor]--;
                        $target[$id] = 1;
                    }
                }
            }

            foreach ($target as $id => $v) {
                $subs[$id]['target'] = $v;
            }
        }

        // ---------- 5. Fill the days ----------
        foreach ($days as $di => $day) {
            $idx = [];

            foreach ($day['slots'] as $si => $slot) {
                if ($slot['type'] === 'study') {
                    $idx[] = $si;
                }
            }

            $n = count($idx);

            if ($n === 0) {
                continue;
            }

            $date = $day['date'];

            $eligible = array_filter($subs, fn ($s) => !$s['exam'] || $date->lt($s['exam']));

            if (count($eligible) === 0) {
                foreach ($idx as $si) {
                    $days[$di]['slots'][$si]['type'] = 'free';
                }

                continue;
            }

            $cap = min(4, max(1, (int) ceil($n / count($eligible))));
            $dayCount = [];
            $picks = [];

            for ($k = 0; $k < $n; $k++) {
                $bestId = null;
                $bestScore = -INF;

                foreach (array_keys($eligible) as $id) {
                    if (($dayCount[$id] ?? 0) >= $cap) {
                        continue;
                    }

                    $s = $subs[$id];                    // always read the current counts
                    $remaining = $s['target'] - $s['given'];
                    $since = $s['last'] ? min(5, (int) abs($s['last']->diffInDays($date))) : 3;

                    $score = $remaining > 0
                        ? $remaining * (1 + 0.6 * $since)
                        : 0.1 * $s['w'] / (1 + $s['given']);

                    $score -= ($dayCount[$id] ?? 0) * 1.5;
                    $score += $s['w'] * 0.001;          // stable tie-break

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestId = $id;
                    }
                }

                if ($bestId === null) {
                    break;
                }

                $picks[] = $bestId;
                $dayCount[$bestId] = ($dayCount[$bestId] ?? 0) + 1;
                $subs[$bestId]['given']++;
                $subs[$bestId]['last'] = $date;
            }

            // Energy matching: best energy slots first, hardest subjects first
            $slotOrder = $idx;

            usort($slotOrder, function ($a, $b) use ($day, $prefs) {
                $ea = $this->energy($day['slots'][$a]['start_min'], $prefs['peak']);
                $eb = $this->energy($day['slots'][$b]['start_min'], $prefs['peak']);

                return $eb <=> $ea ?: $day['slots'][$a]['start_min'] <=> $day['slots'][$b]['start_min'];
            });

            usort($picks, fn ($a, $b) => [$subs[$b]['diff'], $subs[$b]['w']] <=> [$subs[$a]['diff'], $subs[$a]['w']]);

            $assigned = [];                              // slot index => subject id

            foreach ($slotOrder as $j => $si) {
                if (isset($picks[$j])) {
                    $assigned[$si] = $picks[$j];
                } else {
                    $days[$di]['slots'][$si]['type'] = 'free';
                }
            }

            // Interleaving: avoid the same subject in neighbouring blocks
            $seq = array_keys($assigned);
            sort($seq);
            $ids = array_map(fn ($si) => $assigned[$si], $seq);
            $ids = $this->separate($ids);

            // Describe each block in time order
            foreach ($seq as $pos => $si) {
                $sid = $ids[$pos];
                $s = &$subs[$sid];

                $task = !empty($s['queue']) ? array_shift($s['queue']) : null;
                $strategy = null;

                if (!$task) {
                    $list = self::strategies($s['diff']);
                    $strategy = $list[$s['strat_n'] % count($list)];

                    if ($s['days_to_exam'] !== null && $s['days_to_exam'] <= 10 && $s['strat_n'] % 2 === 1) {
                        foreach ($list as $option) {
                            if ($option['name'] === 'Practice test') {
                                $strategy = $option;
                            }
                        }
                    }

                    $s['strat_n']++;
                }

                $s['sessions']++;
                $s['minutes'] += $focusMin;

                $slot = $days[$di]['slots'][$si];
                $slot['type'] = 'study';
                $slot['subject_id'] = $s['id'];
                $slot['subject'] = $s['name'];
                $slot['difficulty'] = $s['diff'];
                $slot['activity'] = $task ? 'Work on a task' : $strategy['name'];
                $slot['detail'] = $task ? null : $strategy['how'];
                $slot['task'] = $task ? $task->task_name : null;
                $slot['task_due'] = ($task && $task->deadline) ? $task->deadline->format('M d') : null;
                $slot['focus_minutes'] = $focusMin;
                $slot['late'] = $day['today'] && $slot['end_min'] <= $nowMin;

                $days[$di]['slots'][$si] = $slot;
                unset($s);
            }
        }

        // ---------- 6. Items, events, totals ----------
        $totalMinutes = 0;
        $sessionCount = 0;
        $bufferCount = 0;

        foreach ($days as $di => $day) {
            $items = [];

            foreach ($day['busy'] as [$bs, $be, $label]) {
                $items[] = [
                    'type'      => 'busy',
                    'start_min' => $bs,
                    'start'     => self::fromMin($bs),
                    'end'       => self::fromMin($be),
                    'label'     => $label,
                ];
            }

            foreach ($day['slots'] as $slot) {
                $slot['late'] = $slot['late'] ?? ($day['today'] && $slot['end_min'] <= $nowMin);
                $items[] = $slot;

                if ($slot['type'] === 'study') {
                    $days[$di]['minutes'] += $slot['focus_minutes'];
                    $totalMinutes += $slot['focus_minutes'];
                    $sessionCount++;
                } elseif ($slot['type'] === 'buffer') {
                    $bufferCount++;
                }
            }

            usort($items, fn ($a, $b) => $a['start_min'] <=> $b['start_min']);
            $days[$di]['items'] = $items;

            foreach ($subs as $s) {
                if ($s['exam'] && $s['exam']->isSameDay($day['date'])) {
                    $days[$di]['events'][] = ['kind' => 'exam', 'text' => $s['name'] . ' exam'];
                }
            }

            foreach ($tasks as $t) {
                if ($t->deadline && $t->deadline->isSameDay($day['date'])) {
                    $days[$di]['events'][] = ['kind' => 'due', 'text' => 'Due: ' . $t->task_name];
                }
            }
        }

        foreach ($subs as $s) {
            if ($s['days_to_exam'] !== null && $s['days_to_exam'] >= 0 && $s['days_to_exam'] <= 14 && $s['sessions'] === 0 && $normalCount > 0) {
                $warnings[] = 'No study blocks fit before the ' . $s['name'] . ' exam this week. Add study windows or free up time.';
            }
        }

        $summary = [
            'minutes'  => $totalMinutes,
            'sessions' => $sessionCount,
            'buffer'   => $bufferCount,
        ];

        $subjectRows = [];

        foreach ($subs as $s) {
            $subjectRows[] = [
                'id'           => $s['id'],
                'name'         => $s['name'],
                'diff'         => $s['diff'],
                'credit'       => $s['credit'],
                'exam'         => $s['exam'],
                'days_to_exam' => $s['days_to_exam'],
                'sessions'     => $s['sessions'],
                'minutes'      => $s['minutes'],
            ];
        }

        usort($subjectRows, fn ($a, $b) => [$b['diff'], $b['sessions']] <=> [$a['diff'], $a['sessions']]);

        return [
            'days'     => $days,
            'subjects' => $subjectRows,
            'summary'  => $summary,
            'warnings' => $warnings,
            'block'    => ['length' => $blockLen, 'focus' => $cfg['focus'], 'rest' => $cfg['rest'], 'label' => $cfg['label']],
        ];
    }

    /**
     * A very short "minimum viable day" for low-energy days.
     */
    public function minimumDay(Collection $subjects, Collection $tasks): array
    {
        $today = Carbon::today();
        $steps = [];

        $steps[] = [
            'minutes' => 2,
            'title'   => 'Get set up',
            'detail'  => 'Put your phone out of reach, fill a glass of water and open your notes or textbook.',
        ];

        $rank = ['High' => 0, 'Medium' => 1, 'Low' => 2];

        $task = $tasks
            ->filter(fn ($t) => $t->deadline)
            ->sortBy(fn ($t) => [$t->deadline->timestamp, $rank[$t->priority] ?? 1])
            ->first();

        if ($task) {
            $steps[] = [
                'minutes' => 25,
                'title'   => 'Start your most urgent task: ' . $task->task_name,
                'detail'  => 'Work on it for 25 minutes and stop when the timer ends. Starting is the hard part, and this counts as a full win.',
            ];
        }

        $best = null;
        $bestScore = -INF;

        foreach ($subjects as $s) {
            $diff = (int) ($s->difficulty ?? 2);
            $score = $diff * 2;

            if ($s->exam_date) {
                $days = (int) $today->diffInDays(Carbon::parse($s->exam_date)->startOfDay(), false);

                if ($days >= 0 && $days <= 14) {
                    $score += 15 - $days;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $s;
            }
        }

        if ($best) {
            $steps[] = [
                'minutes' => 25,
                'title'   => 'Quick recall: ' . $best->subject_name,
                'detail'  => 'Close your notes and write down everything you remember about the last topic you studied. Then check what you missed.',
            ];
        }

        $steps[] = [
            'minutes' => 3,
            'title'   => 'Plan tomorrow',
            'detail'  => 'Write one line: what is the first thing you will study tomorrow?',
        ];

        return [
            'total' => array_sum(array_column($steps, 'minutes')),
            'steps' => $steps,
        ];
    }

    // ---------- helpers ----------

    private function freeIntervals(int $start, int $end, array $busy): array
    {
        $free = [[$start, $end]];

        foreach ($busy as [$bs, $be]) {
            $next = [];

            foreach ($free as [$a, $b]) {
                if ($be <= $a || $bs >= $b) {
                    $next[] = [$a, $b];
                    continue;
                }

                if ($bs > $a) {
                    $next[] = [$a, $bs];
                }

                if ($be < $b) {
                    $next[] = [$be, $b];
                }
            }

            $free = $next;
        }

        usort($free, fn ($x, $y) => $x[0] <=> $y[0]);

        return $free;
    }

    // 2 = in the student's peak period, 1 = next to it, 0 = far from it
    private function energy(int $startMin, string $peak): int
    {
        $period = $startMin < 12 * 60 ? 0 : ($startMin < 17 * 60 ? 1 : 2);
        $peakIndex = ['morning' => 0, 'afternoon' => 1, 'evening' => 2][$peak] ?? 2;
        $distance = abs($period - $peakIndex);

        return $distance === 0 ? 2 : ($distance === 1 ? 1 : 0);
    }

    // Swap subjects around until as few neighbours as possible are the same subject
    private function separate(array $ids): array
    {
        $conflicts = function (array $a): int {
            $count = 0;

            for ($i = 1; $i < count($a); $i++) {
                if ($a[$i] === $a[$i - 1]) {
                    $count++;
                }
            }

            return $count;
        };

        $current = $conflicts($ids);

        for ($pass = 0; $pass < 10 && $current > 0; $pass++) {
            $improved = false;
            $n = count($ids);

            for ($i = 0; $i < $n - 1; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    if ($ids[$i] === $ids[$j]) {
                        continue;
                    }

                    [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
                    $new = $conflicts($ids);

                    if ($new < $current) {
                        $current = $new;
                        $improved = true;
                    } else {
                        [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
                    }
                }
            }

            if (!$improved) {
                break;
            }
        }

        return $ids;
    }
}