<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class CompletedTaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::whereHas('subject', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('subject')
            ->where('status', 'Completed')
            ->orderByDesc('updated_at')
            ->get();

        return view('completed_tasks.index', [
            'tasks' => $tasks,
        ]);
    }
}