<?php

namespace App\Http\Controllers;

use App\DataStructures\PriorityQueue;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class PriorityTaskController extends Controller
{
    // DATA STRUCTURE: Priority Queue (High -> Medium -> Low)
    public function index()
    {
        $userId = Auth::id();

        // Pending tasks, nearest deadline first (breaks ties inside a priority group)
        $pending = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->where('status', 'Pending')
        ->orderBy('deadline')
        ->get();

        // Enqueue every task with its priority
        $pq = new PriorityQueue();

        foreach ($pending as $task) {
            $pq->enqueue($task, $task->priority);
        }

        $groups = $pq->groups();        // High / Medium / Low before processing

        // Dequeue: the most important task always leaves first
        $tasks = [];

        while (!$pq->isEmpty()) {
            $tasks[] = $pq->dequeue();
        }

        return view('priority_tasks.index', [
            'tasks'  => $tasks,
            'groups' => $groups,
            'trace'  => $pq->trace(),
        ]);
    }
}