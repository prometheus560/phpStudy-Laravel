<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = auth()->user()
            ->subjects()
            ->withCount('tasks')
            ->get();

        return view('subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:100'],
        ]);

        auth()->user()->subjects()->create([
            'subject_name' => $validated['subject_name'],
        ]);

        return redirect()->route('subjects.index');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->user_id !== auth()->id()) {
            abort(403);
        }

        $subject->delete();

        return redirect()->route('subjects.index');
    }
}