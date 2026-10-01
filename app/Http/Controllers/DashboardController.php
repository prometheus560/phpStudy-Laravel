<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Task;

class DashboardController extends Controller
{
    // Change these if your database stores different spellings.
    private const PENDING = 'Pending';
    private const DONE    = 'Completed';

    public function index()
    {
        $uid = auth()->id();

        $subjects = Subject::where('user_id', $uid)
            ->withCount([
                'tasks as pending_count' => fn ($q) => $q->where('status', self::PENDING),
                'tasks as done_count'    => fn ($q) => $q->where('status', self::DONE),
            ])
            ->get();

        $pendingTasks = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::PENDING)
            ->orderBy('deadline')
            ->get();

        $recent = Task::with('subject')
            ->where('user_id', $uid)
            ->where('status', self::DONE)
            ->orderByDesc('updated_at')
            ->take(4)
            ->get();

        return view('dashboard', [
            'subjectCount'   => $subjects->count(),
            'pendingCount'   => $pendingTasks->count(),
            'completedCount' => Task::where('user_id', $uid)->where('status', self::DONE)->count(),
            'highCount'      => $pendingTasks->where('priority', 'High')->count(),
            'overdueCount'   => $pendingTasks->filter(fn ($t) => $t->is_overdue)->count(),
            'active'         => $pendingTasks->take(4),
            'next'           => $pendingTasks->first(),
            'subjects'       => $subjects,
            'recent'         => $recent,
        ]);
    }
}