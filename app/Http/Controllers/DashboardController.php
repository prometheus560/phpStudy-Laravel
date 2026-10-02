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

        $hour = now()->hour;

        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        $subjects = Subject::where('user_id', $uid)
            ->withCount([
                'tasks as tasks_count',
                'tasks as pending_tasks_count' => fn ($query) =>
                    $query->where('status', self::PENDING),
            ])
            ->get();

        $pendingTasks = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::PENDING)
            ->orderBy('deadline')
            ->get();

        $recentCompleted = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::DONE)
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        $upcoming = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::PENDING)
            ->whereBetween('deadline', [
                today(),
                today()->copy()->addDays(7),
            ])
            ->orderBy('deadline')
            ->get();

        $dueSoon = Task::where('user_id', $uid)
            ->where('status', self::PENDING)
            ->whereBetween('deadline', [
                today(),
                today()->copy()->addDays(3),
            ])
            ->count();

        $workbench = $pendingTasks->take(4);

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