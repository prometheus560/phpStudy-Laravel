<?php

namespace App\Http\Controllers;

use App\DataStructures\Queue;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class UpcomingTaskController extends Controller
{
    // DATA STRUCTURE: Queue (FIFO)
    public function index()
    {
        $userId = Auth::id();

        // Pending tasks, nearest deadline first (the order they join the queue)
        $pending = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->where('status', 'Pending')
        ->orderBy('deadline')
        ->get();

        // Enqueue every task at the rear
        $queue = new Queue();

        foreach ($pending as $task) {
            $queue->enqueue($task);
        }

        $waiting = $queue->toArray();   // what the queue looks like before processing

        // Dequeue from the front until the queue is empty
        $tasks = [];

        while (!$queue->isEmpty()) {
            $tasks[] = $queue->dequeue();
        }

        return view('upcoming_tasks.index', [
            'tasks'   => $tasks,
            'waiting' => $waiting,
            'trace'   => $queue->trace(),
        ]);
    }
}