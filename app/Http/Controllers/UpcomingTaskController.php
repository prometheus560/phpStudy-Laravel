<?php

namespace App\Http\Controllers;

use App\DataStructures\Queue;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UpcomingTaskController extends Controller
{
   
    public function index(): View
    {
        // Pending tasks for the authenticated user, nearest deadline first.
        $pending = $this->pendingTasksForUser(Auth::id());

        // Build the queue: enqueue each task at the rear.
        $queue = new Queue();

        foreach ($pending as $task) {
            $queue->enqueue($task);
        }

        // Snapshot of the queue before processing.
        $waiting = $queue->toArray();

        // Dequeue from the front until the queue is empty.
        $tasks = [];

        while (! $queue->isEmpty()) {
            $tasks[] = $queue->dequeue();
        }

        return view('upcoming_tasks.index', [
            'tasks'   => $tasks,
            'waiting' => $waiting,
            'trace'   => $queue->trace(),
        ]);
    }


    private function pendingTasksForUser(int $userId)
    {
        return Task::query()
            ->whereHas('subject', fn ($query) => $query->where('user_id', $userId))
            ->with('subject')
            ->where('status', 'Pending')
            ->orderBy('deadline')
            ->get();
    }
}