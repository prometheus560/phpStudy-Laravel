<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Task;

class DashboardController extends Controller
{
    // Any status in this list counts as "completed". Everything else counts as pending.
    private const DONE = ['completed', 'done'];

    // All tasks that belong to the logged-in user's subjects
    private function myTasks(int $uid)
    {
        return Task::with('subject')
            ->whereHas('subject', fn ($q) => $q->where('user_id', $uid));
    }

    public function index()
    {
        $user = auth()->user();
        $uid  = $user->id;

        $hour     = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        // One query for every task, then split it here (fewer database trips = faster page)
        $allTasks = $this->myTasks($uid)->get();

        $isDone = fn ($t) => in_array(strtolower((string) $t->status), self::DONE, true);

        $pendingTasks = $allTasks
            ->reject($isDone)
            ->sortBy(fn ($t) => $t->deadline->timestamp)
            ->values();

        $completedTasks = $allTasks->filter($isDone);

        $workbench = $pendingTasks->take(4);

        $recentCompleted = $completedTasks
            ->sortByDesc(fn ($t) => optional($t->updated_at)->timestamp ?? 0)
            ->take(4)
            ->values();

        $subjects = Subject::where('user_id', $uid)
            ->withCount([
                'tasks',
                'tasks as pending_tasks_count' => fn ($q) => $q->whereNotIn('status', ['Completed', 'completed', 'Done', 'done']),
            ])
            ->get();

        $stats = [
            'subjects'  => $subjects->count(),
            'pending'   => $pendingTasks->count(),
            'completed' => $completedTasks->count(),
            'high'      => $pendingTasks->where('priority', 'High')->count(),
            'overdue'   => $pendingTasks->filter(fn ($t) => $t->deadline->lt(today()))->count(),
        ];

        // Tasks due within the next 3 days (shown in the yellow banner)
        $dueSoonTasks = $pendingTasks->filter(
            fn ($t) => $t->deadline->gte(today()) && $t->deadline->lte(today()->addDays(3))
        )->values();

        $dueSoon = $dueSoonTasks->count();

        // Tasks due in the next 7 days (used in the greeting line)
        $dueThisWeek = $pendingTasks->filter(
            fn ($t) => $t->deadline->gte(today()) && $t->deadline->lte(today()->addDays(7))
        )->count();

        // NEW: study sessions from today onward (today's first)
        $sessions = Schedule::whereHas('subject', fn ($q) => $q->where('user_id', $uid))
            ->with('subject')
            ->whereDate('study_date', '>=', today())
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'greeting', 'user', 'dueSoon', 'dueSoonTasks', 'dueThisWeek', 'stats',
            'workbench', 'recentCompleted', 'subjects', 'sessions'
        ));
    }
}