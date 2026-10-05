<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Task;

class DashboardController extends Controller
{
    // Any status in this list counts as "completed". Everything else counts as pending.
    private const DONE = ['Completed', 'completed', 'Done', 'done'];

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

        $pendingTasks = $this->myTasks($uid)
            ->whereNotIn('status', self::DONE)
            ->orderBy('deadline')
            ->get();

        $workbench = $pendingTasks->take(4);

        $upcoming = $this->myTasks($uid)
            ->whereNotIn('status', self::DONE)
            ->whereDate('deadline', '>=', today())
            ->whereDate('deadline', '<=', today()->addDays(7))
            ->orderBy('deadline')
            ->take(5)
            ->get();

        $recentCompleted = $this->myTasks($uid)
            ->whereIn('status', self::DONE)
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        $subjects = Subject::where('user_id', $uid)
            ->withCount([
                'tasks',
                'tasks as pending_tasks_count' => fn ($q) => $q->whereNotIn('status', self::DONE),
            ])
            ->get();

        $stats = [
            'subjects'  => $subjects->count(),
            'pending'   => $pendingTasks->count(),
            'completed' => $this->myTasks($uid)->whereIn('status', self::DONE)->count(),
            'high'      => $pendingTasks->where('priority', 'High')->count(),
            'overdue'   => $pendingTasks->filter(fn ($t) => $t->deadline->lt(today()))->count(),
        ];

        $dueSoon = $pendingTasks->filter(
            fn ($t) => $t->deadline->gte(today()) && $t->deadline->lte(today()->addDays(3))
        )->count();

        return view('dashboard', compact(
            'greeting', 'user', 'dueSoon', 'stats',
            'workbench', 'upcoming', 'recentCompleted', 'subjects'
        ));
    }
}