<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $query = $user->notes()
            ->with('subject');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $notes = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $subjects = $user->subjects()
            ->orderBy('subject_name')
            ->get();

        return view('notes.index', compact('notes', 'subjects'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if (!empty($validated['subject_id'])) {
            $subjectExists = $user->subjects()
                ->where('id', $validated['subject_id'])
                ->exists();

            if (!$subjectExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subject_id' => 'Invalid subject selected.',
                    ]);
            }
        }

        $validated['user_id'] = $user->id;

        Note::create($validated);

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note added successfully.');
    }

    public function update(Request $request, Note $note)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($note->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if (!empty($validated['subject_id'])) {
            $subjectExists = $user->subjects()
                ->where('id', $validated['subject_id'])
                ->exists();

            if (!$subjectExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'subject_id' => 'Invalid subject selected.',
                    ]);
            }
        }

        $note->update($validated);

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($note->user_id !== $user->id) {
            abort(403);
        }

        $note->delete();

        return redirect()
            ->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }
}