<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private const PENDING = 'Pending';
    private const DONE = 'Completed';

    public function index(Request $request)
    {
        $user = $request->user();
        $uid = $user->id;

        // Greeting
        $hour = now()->hour;

        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        // Subjects
        $subjects = Subject::where('user_id', $uid)
            ->withCount([
                'tasks as tasks_count',
                'tasks as pending_tasks_count' => fn ($query) =>
                    $query->where('status', self::PENDING),
            ])
            ->get();

        // Pending tasks
        $pendingTasks = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::PENDING)
            ->orderBy('deadline')
            ->get();

        // Recently completed tasks
        $recentCompleted = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::DONE)
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        // Upcoming tasks
        $upcoming = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::PENDING)
            ->whereBetween('deadline', [
                today(),
                today()->copy()->addDays(7),
            ])
            ->orderBy('deadline')
            ->get();

        // Tasks due within 3 days
        $dueSoon = Task::where('user_id', $uid)
            ->where('status', self::PENDING)
            ->whereBetween('deadline', [
                today(),
                today()->copy()->addDays(3),
            ])
            ->count();

        // Workbench
        $workbench = $pendingTasks->take(4);

        // Dashboard statistics
        $stats = [
            'subjects' => $subjects->count(),

            'pending' => $pendingTasks->count(),

            'completed' => Task::where('user_id', $uid)
                ->where('status', self::DONE)
                ->count(),

            'high' => $pendingTasks
                ->where('priority', 'High')
                ->count(),

            'overdue' => $pendingTasks
                ->filter(fn ($task) => $task->deadline->lt(today()))
                ->count(),
        ];

        return view('dashboard', [
            'user' => $user,
            'greeting' => $greeting,
            'stats' => $stats,
            'workbench' => $workbench,
            'recentCompleted' => $recentCompleted,
            'upcoming' => $upcoming,
            'subjects' => $subjects,
            'dueSoon' => $dueSoon,
        ]);
    }
}