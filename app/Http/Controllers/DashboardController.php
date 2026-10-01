<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base query: only this user's tasks
        $userTasks = function () {
            return Task::whereHas('subject', function ($query) {
                $query->where('user_id', Auth::id());
            })->with('subject');
        };

        $today = today();

        // Greeting based on the time of day
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        // Stat cards
        $stats = [
            'subjects'  => $user->subjects()->count(),
            'pending'   => $userTasks()->where('status', 'Pending')->count(),
            'completed' => $userTasks()->where('status', 'Completed')->count(),
            'overdue'   => $userTasks()->where('status', 'Pending')->whereDate('deadline', '<', $today)->count(),
            'high'      => $userTasks()->where('status', 'Pending')->where('priority', 'High')->count(),
        ];

        // Banner: pending tasks due in the next 3 days (including today)
        $dueSoon = $userTasks()
            ->where('status', 'Pending')
            ->whereDate('deadline', '>=', $today)
            ->whereDate('deadline', '<=', $today->copy()->addDays(3))
            ->count();

        // Active workbench: pending tasks, soonest deadline first
        $workbench = $userTasks()
            ->where('status', 'Pending')
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        // Upcoming: pending tasks due in the next 7 days
        $upcoming = $userTasks()
            ->where('status', 'Pending')
            ->whereDate('deadline', '>=', $today)
            ->whereDate('deadline', '<=', $today->copy()->addDays(7))
            ->orderBy('deadline')
            ->limit(4)
            ->get();

        // Recently completed
        $recentCompleted = $userTasks()
            ->where('status', 'Completed')
            ->orderByDesc('deadline')
            ->limit(3)
            ->get();

        // Subject overview (task counts per subject)
        $subjects = $user->subjects()
            ->withCount([
                'tasks',
                'tasks as pending_tasks_count' => function ($query) {
                    $query->where('status', 'Pending');
                },
            ])
            ->get();

        return view('dashboard', compact(
            'user',
            'greeting',
            'stats',
            'dueSoon',
            'workbench',
            'upcoming',
            'recentCompleted',
            'subjects'
        ));
    }
}