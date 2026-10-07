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

        // "sometimes" lets the Complete / Reopen buttons send only the status,
        // while the Edit form can send every field.
        $data = $request->validate([
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'task_name'  => 'sometimes|required|string|max:255',
            'deadline'   => 'sometimes|required|date',
            'priority'   => 'sometimes|required|in:High,Medium,Low',
            'status'     => 'sometimes|required|in:Pending,Completed',
        ]);

        if (isset($data['subject_id'])) {
            Subject::where('id', $data['subject_id'])
                ->where('user_id', auth()->id())
                ->firstOrFail();
        }

        $task->update($data);

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