<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subject;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('subject')
        ->where('user_id', auth()->id())
        ->orderBy('deadline')
        ->get();

    $subjects = Subject::where('user_id', auth()->id())
        ->orderBy('subject_name')
        ->get();

    return view('tasks.index', compact('tasks', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::where('user_id', auth()->id())
            ->orderBy('subject_name')
            ->get();

        return view('tasks.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'task_name' => 'required|string|max:255',
            'deadline' => 'required|date',
            'priority' => 'required|string',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'subject_id' => $request->subject_id,
            'task_name' => $request->task_name,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => 'Pending',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task added successfully.');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'task_name' => 'required|string|max:255',
            'deadline' => 'required|date',
            'priority' => 'required|string',
            'status' => 'required|string',
        ]);

        $task->update([
            'subject_id' => $request->subject_id,
            'task_name' => $request->task_name,
            'deadline' => $request->deadline,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}