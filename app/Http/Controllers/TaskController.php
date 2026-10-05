<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Subject;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Only tasks that belong to the logged-in user's subjects
    private function ownedByMe($query)
    {
        return $query->whereHas('subject', fn ($q) => $q->where('user_id', auth()->id()));
    }

    // Stops a user from loading or changing another user's task
    private function authorizeTask(Task $task): void
    {
        $task->loadMissing('subject');

        if (!$task->subject || $task->subject->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function index()
    {
        $tasks = $this->ownedByMe(Task::with('subject'))
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
            'task_name'  => 'required|string|max:255',
            'deadline'   => 'required|date',
            'priority'   => 'required|string',
        ]);

        // The chosen subject must belong to this user
        Subject::where('id', $request->subject_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Task::create([
            'user_id'    => auth()->id(),
            'subject_id' => $request->subject_id,
            'task_name'  => $request->task_name,
            'deadline'   => $request->deadline,
            'priority'   => $request->priority,
            'status'     => 'Pending',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task added successfully.');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'task_name'  => 'required|string|max:255',
            'deadline'   => 'required|date',
            'priority'   => 'required|string',
            'status'     => 'required|string',
        ]);

        Subject::where('id', $request->subject_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $task->update([
            'subject_id' => $request->subject_id,
            'task_name'  => $request->task_name,
            'deadline'   => $request->deadline,
            'priority'   => $request->priority,
            'status'     => $request->status,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    // NEW: used by the Done button on the dashboard
    public function complete(Task $task)
    {
        $this->authorizeTask($task);

        $task->update(['status' => 'Completed']);

        return back()->with('success', 'Task marked as completed.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}