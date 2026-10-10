<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BinarySearchController extends Controller
{
    public function index()
    {
      
        $subjects = Auth::user()
            ->subjects()
            ->withCount([
                'tasks',
                'tasks as pending_tasks_count' => function ($query) {
                    $query->where('status', 'Pending');
                },
            ])
            ->get()
            ->sortBy(fn ($subject) => mb_strtolower($subject->subject_name))
            ->values();

     

        return view('binary_search.index', compact('subjects'));
    }
}