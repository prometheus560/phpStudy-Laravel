<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Task;

class DashboardController extends Controller
{
    private const DONE = ['completed', 'done'];

    private function myTasks(int $userId)
    {
        return Task::with('subject')
            ->whereHas('subject', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
    }

    public function index()
    {
        $user = auth()->user();

        abort_unless($user, 403);

        $userId = $user->id;
        $today = today();

        $hour = now()->hour;

        $greeting = $hour < 12
            ? 'Good morning'
            : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        $allTasks = $this->myTasks($userId)->get();

        $isDone = fn ($task) => in_array(
            strtolower((string) $task->status),
            self::DONE,
            true
        );

        $pendingTasks = $allTasks
            ->reject($isDone)
            ->sortBy(fn ($task) => $task->deadline
                ? $task->deadline->timestamp
                : PHP_INT_MAX
            )
            ->values();

        $completedTasks = $allTasks->filter($isDone);

        $subjects = Subject::where('user_id', $userId)
            ->withCount([
                'tasks',
                'tasks as pending_tasks_count' => function ($query) {
                    $query->whereNotIn('status', [
                        'completed', 'Completed',
                        'done', 'Done',
                    ]);
                },
            ])
            ->get();

        $stats = [
            'subjects' => $subjects->count(),
            'pending' => $pendingTasks->count(),
            'completed' => $completedTasks->count(),
            'high' => $pendingTasks
                ->filter(fn ($task) =>
                    strtolower((string) $task->priority) === 'high'
                )
                ->count(),
            'overdue' => $pendingTasks
                ->filter(fn ($task) =>
                    $task->deadline &&
                    $task->deadline->lt($today)
                )
                ->count(),
        ];

        $workbench = $pendingTasks->take(4);

        $recentCompleted = $completedTasks
            ->sortByDesc(fn ($task) =>
                $task->updated_at?->timestamp ?? 0
            )
            ->take(4)
            ->values();

        $dueSoonTasks = $pendingTasks
            ->filter(fn ($task) =>
                $task->deadline &&
                $task->deadline->gte($today) &&
                $task->deadline->lte($today->copy()->addDays(3))
            )
            ->values();

        $dueSoon = $dueSoonTasks->count();

        $dueThisWeek = $pendingTasks
            ->filter(fn ($task) =>
                $task->deadline &&
                $task->deadline->gte($today) &&
                $task->deadline->lte($today->copy()->addDays(7))
            )
            ->count();

        $sessions = Schedule::whereHas(
            'subject',
            fn ($query) => $query->where('user_id', $userId)
        )
            ->with('subject')
            ->whereDate('study_date', '>=', $today)
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $pendingCount = $stats['pending'];

        return view('dashboard', compact(
            'user',
            'greeting',
            'stats',
            'subjects',
            'workbench',
            'recentCompleted',
            'sessions',
            'dueSoon',
            'dueSoonTasks',
            'dueThisWeek',
            'pendingCount'
        ));
    }
}
