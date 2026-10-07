<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class SortedTaskController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get the user's tasks in no particular order
        $tasks = Task::whereHas('subject', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('subject')
        ->orderBy('id')
        ->get()
        ->all();

        // Bubble Sort by deadline (earliest first)
        $n = count($tasks);
        $passes = [];
        $comparisons = 0;
        $swaps = 0;

        for ($i = 0; $i < $n - 1; $i++) {

            $swapsThisPass = 0;

            for ($j = 0; $j < $n - $i - 1; $j++) {

                $comparisons++;

                if ($tasks[$j]->deadline->gt($tasks[$j + 1]->deadline)) {

                    $temp = $tasks[$j];
                    $tasks[$j] = $tasks[$j + 1];
                    $tasks[$j + 1] = $temp;

                    $swapsThisPass++;
                    $swaps++;
                }
            }

            // Remember what the list looked like after this pass
            $passes[] = [
                'number' => $i + 1,
                'swaps'  => $swapsThisPass,
                'order'  => array_map(fn ($t) => $t->task_name, $tasks),
            ];

            // No swaps means everything is already in order
            if ($swapsThisPass === 0) {
                break;
            }
        }

        return view('sorted_tasks.index', [
            'tasks'       => $tasks,
            'passes'      => $passes,
            'comparisons' => $comparisons,
            'swaps'       => $swaps,
        ]);
    }
}