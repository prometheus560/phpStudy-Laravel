<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\StudyPreference;
use App\Models\Subject;
use App\Models\Task;
use App\Services\StudyPlanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudyPlanController extends Controller
{
    private const DONE = ['Completed', 'completed', 'Done', 'done'];

    public function __construct(private StudyPlanner $planner)
    {
    }

    /**
     * The weekly master timetable.
     */
    public function index(Request $request)
    {
        $prefs = $this->preferences();
        $subjects = $this->subjects();

        if (!$prefs || $subjects->isEmpty()) {
            return redirect()->route('study_plan.setup');
        }

        $week = $this->weekOffset($request->query('w'));

        $plan = $this->buildPlan($subjects, $prefs, $week);

        return view('study_plan.index', [
            'plan'     => $plan,
            'week'     => $week,
            'start'    => $plan['days'][0]['date'],
            'end'      => $plan['days'][6]['date'],
            'strategy' => fn (int $difficulty) => StudyPlanner::strategies($difficulty),
        ]);
    }

    /**
     * Setup form: subject difficulty and exams, study windows, session style, commitments.
     */
    public function setup()
    {
        $prefs = $this->preferences();

        return view('study_plan.setup', [
            'subjects'   => $this->subjects(),
            'prefs'      => $prefs ? $this->prefsArray($prefs) : StudyPlanner::defaults(),
            'configured' => (bool) $prefs,
            'days'       => StudyPlanner::DAY_NAMES,
            'sessions'   => StudyPlanner::SESSIONS,
        ]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subjects'                  => ['nullable', 'array'],
            'subjects.*.difficulty'     => ['required', 'in:1,2,3'],
            'subjects.*.credit_hours'   => ['required', 'integer', 'between:1,6'],
            'subjects.*.exam_date'      => ['nullable', 'date'],

            'session_length'            => ['required', Rule::in(['25', '50', '90'])],
            'peak'                      => ['required', Rule::in(['morning', 'afternoon', 'evening'])],

            'windows'                   => ['required', 'array'],
            'windows.*.start'           => ['nullable', 'date_format:H:i'],
            'windows.*.end'             => ['nullable', 'date_format:H:i'],

            'commitments'               => ['nullable', 'array', 'max:40'],
            'commitments.*.day'         => ['nullable', Rule::in(StudyPlanner::DAYS)],
            'commitments.*.start'       => ['nullable', 'date_format:H:i'],
            'commitments.*.end'         => ['nullable', 'date_format:H:i'],
            'commitments.*.label'       => ['nullable', 'string', 'max:60'],
        ], [
            'subjects.*.exam_date.date'      => 'One of the exam dates is not a valid date.',
            'windows.*.start.date_format'    => 'Use a valid start time for each study day.',
            'windows.*.end.date_format'      => 'Use a valid end time for each study day.',
            'commitments.*.start.date_format' => 'Use a valid start time for each commitment.',
            'commitments.*.end.date_format'   => 'Use a valid end time for each commitment.',
        ]);

        $errors = [];

        // Study windows
        $windows = [];

        foreach (StudyPlanner::DAYS as $day) {
            $row = (array) $request->input("windows.$day", []);
            $on = !empty($row['on']);
            $start = (string) ($row['start'] ?? '');
            $end = (string) ($row['end'] ?? '');

            if ($on && ($start === '' || $end === '' || $end <= $start)) {
                $errors["windows.$day"] = StudyPlanner::DAY_NAMES[$day] . ': choose a start time and a later end time.';
            }

            $windows[$day] = [
                'on'    => $on,
                'start' => $start !== '' ? $start : '17:00',
                'end'   => $end !== '' ? $end : '21:00',
            ];
        }

        if (!collect($windows)->contains(fn ($w) => $w['on'])) {
            $errors['windows'] = 'Turn on at least one study day.';
        }

        // Fixed weekly commitments (blank rows are ignored)
        $commitments = [];

        foreach ((array) $request->input('commitments', []) as $i => $row) {
            $day = (string) ($row['day'] ?? '');
            $start = (string) ($row['start'] ?? '');
            $end = (string) ($row['end'] ?? '');
            $label = trim((string) ($row['label'] ?? ''));

            if ($start === '' && $end === '' && $label === '') {
                continue;
            }

            $number = (int) $i + 1;

            if ($day === '' || $start === '' || $end === '' || $end <= $start) {
                $errors["commitments.$i"] = "Commitment $number: choose a day, a start time and a later end time.";
                continue;
            }

            $commitments[] = ['day' => $day, 'start' => $start, 'end' => $end, 'label' => $label];
        }

        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        StudyPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'session_length' => (int) $request->input('session_length'),
                'peak'           => $request->input('peak'),
                'windows'        => $windows,
                'commitments'    => $commitments,
            ]
        );

        // Difficulty, credit hours and exam date, only for this student's own subjects
        $own = $user->subjects()->pluck('id')->all();

        foreach ((array) $request->input('subjects', []) as $id => $row) {
            if (!in_array((int) $id, $own, true)) {
                continue;
            }

            $user->subjects()->whereKey($id)->update([
                'difficulty'   => (int) $row['difficulty'],
                'credit_hours' => (int) $row['credit_hours'],
                'exam_date'    => !empty($row['exam_date']) ? $row['exam_date'] : null,
            ]);
        }

        return redirect()
            ->route('study_plan.index')
            ->with('success', 'Your plan has been updated.');
    }

    /**
     * Save the study blocks of one week into the Schedule page.
     */
    public function schedule(Request $request)
    {
        $prefs = $this->preferences();
        $subjects = $this->subjects();

        if (!$prefs || $subjects->isEmpty()) {
            return redirect()->route('study_plan.setup');
        }

        $week = $this->weekOffset($request->input('w'));
        $plan = $this->buildPlan($subjects, $prefs, $week);

        $created = 0;
        $skipped = 0;

        foreach ($plan['days'] as $day) {
            if ($day['past']) {
                continue;
            }

            foreach ($day['items'] as $item) {
                if ($item['type'] !== 'study') {
                    continue;
                }

                // A block that already ended today is not worth adding
                if (!empty($item['late'])) {
                    continue;
                }

                if ($item['state'] !== 'free') {
                    $skipped++;
                    continue;
                }

                $subject = $subjects->firstWhere('id', $item['subject_id']);

                if (!$subject) {
                    continue;
                }

                $subject->schedules()->create([
                    'study_date' => $day['date']->format('Y-m-d'),
                    'start_time' => $item['start'],
                    'end_time'   => $item['end'],
                ]);

                $created++;
            }
        }

        if ($created === 0) {
            return redirect()
                ->route('study_plan.index', ['w' => $week])
                ->with('info', 'Nothing new to add. Those blocks are already in your schedule or overlap with sessions you planned.');
        }

        $message = $created === 1 ? '1 study block added to your schedule.' : $created . ' study blocks added to your schedule.';

        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' skipped (already planned or overlapping).';
        }

        return redirect()
            ->route('schedule.index')
            ->with('success', $message);
    }

    /**
     * Today's checklist, or the low-energy "minimum viable day".
     */
    public function today(Request $request)
    {
        $prefs = $this->preferences();
        $subjects = $this->subjects();

        if (!$prefs || $subjects->isEmpty()) {
            return redirect()->route('study_plan.setup');
        }

        $mode = $request->query('mode') === 'lite' ? 'lite' : 'full';
        $today = Carbon::today();
        $tasks = $this->pendingTasks();

        $due = fn ($task) => $task->deadline ? (int) $today->diffInDays($task->deadline->copy()->startOfDay(), false) : null;

        $overdue = $tasks->filter(fn ($t) => $due($t) !== null && $due($t) < 0)->values();
        $dueToday = $tasks->filter(fn ($t) => $due($t) === 0)->values();
        $soon = $tasks->filter(fn ($t) => $due($t) !== null && $due($t) >= 1 && $due($t) <= 3)->values();

        $lite = $mode === 'lite' ? $this->planner->minimumDay($subjects, $tasks) : null;

        $todayPlan = null;

        if ($mode === 'full') {
            $plan = $this->buildPlan($subjects, $prefs, 0);

            foreach ($plan['days'] as $day) {
                if ($day['today']) {
                    $todayPlan = $day;
                    break;
                }
            }
        }

        return view('study_plan.today', [
            'mode'      => $mode,
            'today'     => $today,
            'overdue'   => $overdue,
            'dueToday'  => $dueToday,
            'soon'      => $soon,
            'todayPlan' => $todayPlan,
            'lite'      => $lite,
        ]);
    }

    // ---------- helpers ----------

    private function preferences(): ?StudyPreference
    {
        return StudyPreference::where('user_id', Auth::id())->first();
    }

    private function prefsArray(StudyPreference $prefs): array
    {
        return [
            'session'     => (string) $prefs->session_length,
            'peak'        => $prefs->peak,
            'windows'     => $prefs->windows ?? [],
            'commitments' => $prefs->commitments ?? [],
        ];
    }

    private function subjects(): Collection
    {
        return Subject::where('user_id', Auth::id())
            ->orderBy('subject_name')
            ->get();
    }

    private function pendingTasks(): Collection
    {
        return Task::whereHas('subject', fn ($q) => $q->where('user_id', Auth::id()))
            ->with('subject')
            ->whereNotIn('status', self::DONE)
            ->orderBy('deadline')
            ->get();
    }

    private function weekOffset($value): int
    {
        return max(0, min(3, (int) $value));
    }

    private function weekStart(int $week): Carbon
    {
        return Carbon::today()->startOfWeek(Carbon::MONDAY)->addWeeks($week);
    }

    private function buildPlan(Collection $subjects, StudyPreference $prefs, int $week): array
    {
        $plan = $this->planner->generate(
            $subjects,
            $this->prefsArray($prefs),
            $this->pendingTasks(),
            $this->weekStart($week)
        );

        return $this->markSaved($plan);
    }

    /**
     * Tag every study block: "saved" (already in Schedule), "clash" (overlaps another session), or "free".
     */
    private function markSaved(array $plan): array
    {
        $first = $plan['days'][0]['date']->format('Y-m-d');
        $last = $plan['days'][6]['date']->format('Y-m-d');

        $existing = Schedule::whereHas('subject', fn ($q) => $q->where('user_id', Auth::id()))
            ->whereBetween('study_date', [$first, $last])
            ->get()
            ->groupBy(fn ($row) => $row->study_date->format('Y-m-d'));

        foreach ($plan['days'] as $di => $day) {
            $rows = $existing->get($day['date']->format('Y-m-d'), collect());

            foreach ($day['items'] as $ii => $item) {
                if ($item['type'] !== 'study') {
                    continue;
                }

                $state = 'free';

                foreach ($rows as $row) {
                    $rowStart = substr((string) $row->start_time, 0, 5);
                    $rowEnd = substr((string) $row->end_time, 0, 5);

                    if ((int) $row->subject_id === (int) $item['subject_id'] && $rowStart === $item['start'] && $rowEnd === $item['end']) {
                        $state = 'saved';
                        break;
                    }

                    if ($rowStart < $item['end'] && $rowEnd > $item['start']) {
                        $state = 'clash';
                    }
                }

                $plan['days'][$di]['items'][$ii]['state'] = $state;
            }
        }

        return $plan;
    }
}