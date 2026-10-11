<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('schedule.index', compact('subjects', 'schedules'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer'],
            'study_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'end_time.after' => 'The end time must be later than the start time.',
        ]);


        $subject = Subject::where('id', $validated['subject_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();


        // Double-click / slow connection guard: the same session is never saved twice
        $alreadyThere = $subject->schedules()
            ->whereDate('study_date', $validated['study_date'])
            ->where('start_time', $validated['start_time'])
            ->where('end_time', $validated['end_time'])
            ->exists();

        if ($alreadyThere) {
            return redirect()
                ->route('schedule.index')
                ->with('success', 'That study session is already in your schedule.');
        }


        $subject->schedules()->create([
            'study_date' => $validated['study_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);


        return redirect()
            ->route('schedule.index')
            ->with('success', 'Study schedule added successfully.');
    }


    // NEW: edit a study session
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
}