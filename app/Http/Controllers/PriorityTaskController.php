<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class PriorityTaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->where('status', 'Pending')
        ->orderByRaw("
            CASE priority
                WHEN 'High' THEN 1
                WHEN 'Medium' THEN 2
                WHEN 'Low' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('deadline')
        ->get();

        return view('priority_tasks.index', compact('tasks'));
    }
}