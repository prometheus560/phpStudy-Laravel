<?php

namespace App\Http\Controllers;

use App\DataStructures\Stack;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class CompletedTaskController extends Controller
{
    // DATA STRUCTURE: Stack (LIFO)
    public function index()
    {
        $userId = Auth::id();

        // Completed tasks, oldest first (the order they were finished)
        $completed = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->where('status', 'Completed')
        ->orderBy('updated_at')
        ->get();

        // Push every task on top of the stack
        $stack = new Stack();

        foreach ($completed as $task) {
            $stack->push($task);
        }

        $pushed = $stack->toArray();    // bottom -> top

        // Pop from the top until the stack is empty (most recent first)
        $tasks = [];

        while (!$stack->isEmpty()) {
            $tasks[] = $stack->pop();
        }

        return view('completed_tasks.index', [
            'tasks'  => $tasks,
            'pushed' => $pushed,
            'trace'  => $stack->trace(),
        ]);
    }
}