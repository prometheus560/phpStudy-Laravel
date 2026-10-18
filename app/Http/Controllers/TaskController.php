
<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Return only tasks belonging to the logged-in user's subjects.
     */
    private function ownedByMe($query)
    {
        abort_unless(auth()->check(), 401);

        return $query->whereHas('subject', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    /**
     * Prevent users from accessing another user's task.
     */
    private function authorizeTask(Task $task): void
    {
        abort_unless(auth()->check(), 401);

        $task->loadMissing('subject');

        abort_unless(
            $task->subject &&
            (int) $task->subject->user_id === (int) auth()->id(),
            403
        );
    }

    public function index()
    {
        abort_unless(auth()->check(), 401);

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
        abort_unless(auth()->check(), 401);

        $subjects = Subject::where('user_id', auth()->id())
            ->orderBy('subject_name')
            ->get();

        return view('tasks.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check(), 401);

        $data = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'task_name' => 'required|string|max:255',
            'deadline' => 'required|date',
            'priority' => 'required|in:High,Medium,Low',
        ]);

        Subject::where('id', $data['subject_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Task::create([
            'user_id' => auth()->id(),
            'subject_id' => $data['subject_id'],
            'task_name' => $data['task_name'],
            'deadline' => $data['deadline'],
            'priority' => $data['priority'],
            'status' => 'Pending',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task added successfully.');
    }

    public function edit(Task $task)
    {
        $this->authorizeTask($task);

        $subjects = Subject::where('user_id', auth()->id())
            ->orderBy('subject_name')
            ->get();

        return view('tasks.edit', compact('task', 'subjects'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'task_name' => 'sometimes|required|string|max:255',
            'deadline' => 'sometimes|required|date',
            'priority' => 'sometimes|required|in:High,Medium,Low',
            'status' => 'sometimes|required|in:Pending,Completed',
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

    public function complete(Task $task)
    {
        $this->authorizeTask($task);

        $task->update(['status' => 'Completed']);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task marked as completed.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function destroyAll()
    {
        $deletedCount = $this->ownedByMe(Task::query())->delete();

        return redirect()
            ->route('tasks.index')
            ->with(
                $deletedCount > 0 ? 'success' : 'info',
                $deletedCount > 0
                    ? 'All tasks have been deleted.'
                    : 'No tasks to delete.'
            );
    }

    public function bulkDestroy(Request $request)
    {
        abort_unless(auth()->check(), 401);

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|distinct',
        ], [
            'ids.required' => 'Please select at least one task.',
            'ids.min' => 'Please select at least one task.',
        ]);

        $deletedCount = $this->ownedByMe(Task::query())
            ->whereIn('id', $data['ids'])
            ->delete();

        if ($deletedCount === 0) {
            return redirect()
                ->route('tasks.index')
                ->with('info', 'No matching tasks were found.');
        }

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                $deletedCount . (
                    $deletedCount === 1
                        ? ' task deleted successfully.'
                        : ' tasks deleted successfully.'
                )
            );
    }
}
