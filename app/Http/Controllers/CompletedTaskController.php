
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UpcomingTaskController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::check(), 401);

        $userId = Auth::id();

        $tasks = Task::query()
            ->whereHas('subject', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('subject')
            ->where('status', 'Pending')
            ->orderBy('deadline')
            ->get();

        return view('upcoming_tasks.index', [
            'tasks' => $tasks,
        ]);
    }
}
