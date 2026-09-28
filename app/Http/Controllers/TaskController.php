<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $subjects = $user->subjects()
            ->with('tasks')
            ->get();

        $tasks = Task::whereHas('subject', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with('subject')
        ->orderBy('deadline')
        ->get();

        return view('tasks.index', compact('subjects', 'tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'integer'],
            'task_name' => ['required', 'string', 'max:150'],
            'deadline' => ['required', 'date'],
            'priority' => ['required', 'in:High,Medium,Low'],
        ]);

        $user = Auth::user();

        $subject = $user->subjects()
            ->findOrFail($validated['subject_id']);

        $subject->tasks()->create([
            'task_name' => $validated['task_name'],
            'deadline' => $validated['deadline'],
            'priority' => $validated['priority'],
            'status' => 'Pending',
        ]);

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->subject->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Pending,Completed'],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        if ($task->subject->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index');
    }
}