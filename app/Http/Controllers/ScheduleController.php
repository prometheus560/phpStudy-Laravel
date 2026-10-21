<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function index()
    {
        $subjects = Subject::where('user_id', Auth::id())
            ->orderBy('subject_name')
            ->get();

        $schedules = Schedule::whereHas('subject', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with('subject')
        ->orderBy('study_date')
        ->orderBy('start_time')
        ->get();

        // Pending task deadlines, shown on the calendar so study time can be planned around them
        $tasks = Task::where('user_id', Auth::id())
            ->where('status', 'Pending')
            ->whereNotNull('deadline')
            ->with('subject')
            ->orderBy('deadline')
            ->get();

        return view('schedule.index', compact('subjects', 'schedules', 'tasks'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer'],
            'study_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'repeat_weeks' => ['nullable', 'integer', 'min:1', 'max:16'],
        ], [
            'end_time.after' => 'The end time must be later than the start time.',
        ]);


        $subject = Subject::where('id', $validated['subject_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();


        $weeks   = (int) ($validated['repeat_weeks'] ?? 1);
        $first   = Carbon::parse($validated['study_date'])->startOfDay();
        $created = 0;
        $skipped = 0;
        $firstClash = null;

        for ($i = 0; $i < $weeks; $i++) {
            $date = $first->copy()->addWeeks($i)->format('Y-m-d');

            // Double-click / slow connection guard: the same session is never saved twice
            $alreadyThere = $subject->schedules()
                ->whereDate('study_date', $date)
                ->where('start_time', $validated['start_time'])
                ->where('end_time', $validated['end_time'])
                ->exists();

            if ($alreadyThere) {
                $skipped++;
                continue;
            }

            // Two study sessions can't overlap
            $clash = $this->findClash($date, $validated['start_time'], $validated['end_time']);

            if ($clash) {
                $skipped++;
                $firstClash = $firstClash ?? $clash;
                continue;
            }

            $subject->schedules()->create([
                'study_date' => $date,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);

            $created++;
        }


        if ($created === 0 && $firstClash) {
            throw ValidationException::withMessages([
                'start_time' => $this->clashMessage($firstClash),
            ]);
        }

        if ($created === 0) {
            return redirect()
                ->route('schedule.index')
                ->with('success', 'That study session is already in your schedule.');
        }


        $message = $created === 1
            ? 'Study schedule added successfully.'
            : $created . ' study sessions added.';

        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' skipped (already planned or overlapping).';
        }

        return redirect()
            ->route('schedule.index')
            ->with('success', $message);
    }


    public function update(Request $request, Schedule $schedule)
    {
        if ($schedule->subject->user_id !== Auth::id()) {
            abort(403);
        }


        $validated = $request->validate([
            'subject_id' => ['required', 'integer'],
            'study_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'The end time must be later than the start time.',
        ]);


        // The chosen subject must belong to this user
        $subject = Subject::where('id', $validated['subject_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();


        $clash = $this->findClash(
            $validated['study_date'],
            $validated['start_time'],
            $validated['end_time'],
            $schedule->id
        );

        if ($clash) {
            throw ValidationException::withMessages([
                'start_time' => $this->clashMessage($clash),
            ]);
        }


        $schedule->update([
            'subject_id' => $subject->id,
            'study_date' => $validated['study_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);


        return redirect()
            ->route('schedule.index')
            ->with('success', 'Study schedule updated successfully.');
    }


    public function destroy(Schedule $schedule)
    {
        if ($schedule->subject->user_id !== Auth::id()) {
            abort(403);
        }


        $schedule->delete();


        return redirect()
            ->route('schedule.index')
            ->with('success', 'Study schedule deleted successfully.');
    }


    /**
     * Finds one of this user's sessions that overlaps the given time on the given date.
     */
    private function findClash(string $date, string $start, string $end, ?int $ignoreId = null): ?Schedule
    {
        return Schedule::whereHas('subject', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with('subject')
            ->whereDate('study_date', $date)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->first();
    }


    private function clashMessage(Schedule $clash): string
    {
        return 'That time overlaps with ' . $clash->subject->subject_name
            . ' (' . date('g:i A', strtotime($clash->start_time))
            . ' to ' . date('g:i A', strtotime($clash->end_time)) . ').';
    }
}