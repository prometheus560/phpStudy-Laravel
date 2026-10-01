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

    /**
     * Open a file inside the study planner (preview page).
     */
    public function show(StudyFile $studyFile)
    {
        $this->authorizeFile($studyFile);

        $kind = $this->previewKind($studyFile);

        return view('study_files.show', compact('studyFile', 'kind'));
    }

    /**
     * Stream the file inline (no download) so it can be shown in the preview page.
     */
    public function preview(StudyFile $studyFile)
    {
        $this->authorizeFile($studyFile);

        $kind = $this->previewKind($studyFile);

        // Only safe types are shown in the browser; everything else is download-only
        if ($kind === 'none') {
            abort(415, 'This file type cannot be previewed.');
        }

        $disk = Storage::disk('public');
        $mime = $disk->mimeType($studyFile->file_path);

        // Text files are always served as plain text
        if ($kind === 'text') {
            $mime = 'text/plain; charset=UTF-8';
        }

        return response()->file($disk->path($studyFile->file_path), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($studyFile->file_name) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(StudyFile $studyFile)
    {
        $this->authorizeFile($studyFile);

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

    /**
     * Make sure the file belongs to the logged-in user and still exists.
     */
    private function authorizeFile(StudyFile $studyFile): void
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
    }

    /**
     * Decide how a file can be shown: pdf, image, text, or none (download only).
     * Uses the real file content type, not the one the browser reported at upload.
     */
    private function previewKind(StudyFile $studyFile): string
    {
        $mime = Storage::disk('public')->mimeType($studyFile->file_path);

        if ($mime === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp'], true)) {
            return 'image';
        }

        if (is_string($mime) && str_starts_with($mime, 'text/') && $mime !== 'text/html') {
            return 'text';
        }

        return 'none';
    }
}