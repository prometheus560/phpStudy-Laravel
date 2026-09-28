<?php

namespace App\Http\Controllers;

use App\Models\StudyFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudyFileController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $query = $user->studyFiles()
            ->with('subject');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('file_name', 'like', '%' . $search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $files = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $subjects = $user->subjects()
            ->orderBy('subject_name')
            ->get();

        return view('study_files.index', compact('files', 'subjects'));
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
            'file' => 'required|file|max:10240',
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

        $file = $request->file('file');

        $path = $file->store('study-files', 'public');

        StudyFile::create([
            'user_id' => $user->id,
            'subject_id' => $validated['subject_id'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('study_files.index')
            ->with('success', 'File uploaded successfully.');
    }

    public function download(StudyFile $studyFile)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($studyFile->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($studyFile->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $studyFile->file_path,
            $studyFile->file_name
        );
    }

    public function destroy(StudyFile $studyFile)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($studyFile->user_id !== $user->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($studyFile->file_path)) {
            Storage::disk('public')->delete($studyFile->file_path);
        }

        $studyFile->delete();

        return redirect()
            ->route('study_files.index')
            ->with('success', 'File deleted successfully.');
    }
}