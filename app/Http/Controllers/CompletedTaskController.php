<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UpcomingTaskController extends Controller
{
    public function index(): View
    {
        $tasks = $this->pendingTasksForUser(Auth::id());

        return view('upcoming_tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    private function pendingTasksForUser(int $userId)
    {
        return Task::query()
            ->whereHas('subject', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('subject')
            ->where('status', 'Pending')
            ->orderBy('deadline')
            ->get();
    }
}