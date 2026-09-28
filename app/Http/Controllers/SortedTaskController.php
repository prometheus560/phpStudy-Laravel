<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class SortedTaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $tasks = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->orderBy('deadline')
        ->get();

        return view('sorted_tasks.index', compact('tasks'));
    }
}