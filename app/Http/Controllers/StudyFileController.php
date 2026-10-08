<?php

namespace App\Http\Controllers;

use App\Models\StudyFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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

        $path = $file->store('study-files', 'local');

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

        // PowerPoint / Word / Excel: Office Online reads the file through a
        // temporary signed link (valid for 15 minutes).
        $officeUrl = null;

        if ($kind === 'office') {
            $signedUrl = URL::temporarySignedRoute(
                'study_files.office',
                now()->addMinutes(15),
                ['studyFile' => $studyFile->id]
            );

            $officeUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($signedUrl);
        }

        return view('study_files.show', compact('studyFile', 'kind', 'officeUrl'));
    }

    /**
     * Stream the file inline (no download) so it can be shown in the preview page.
     */
    public function preview(StudyFile $studyFile)
    {
        $this->authorizeFile($studyFile);

        $kind = $this->previewKind($studyFile);

        // Only safe types are shown in the browser; everything else is download-only
        if (!in_array($kind, ['pdf', 'image', 'text'], true)) {
            abort(415, 'This file type cannot be previewed.');
        }

        $disk = Storage::disk('local');
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

    /**
     * Serves an Office file to Microsoft's online viewer.
     * Public route, but protected by a temporary signed link (see routes).
     */
    public function office(StudyFile $studyFile)
    {
        if (!Storage::disk('local')->exists($studyFile->file_path)) {
            abort(404);
        }

        if ($this->previewKind($studyFile) !== 'office') {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($studyFile->file_path), [
            'Content-Type' => $this->officeMime($studyFile),
            'Content-Disposition' => 'attachment; filename="' . addslashes($studyFile->file_name) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(StudyFile $studyFile)
    {
        $this->authorizeFile($studyFile);

        return Storage::disk('local')->download(
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

        if (Storage::disk('local')->exists($studyFile->file_path)) {
            Storage::disk('local')->delete($studyFile->file_path);
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

        if (!Storage::disk('local')->exists($studyFile->file_path)) {
            abort(404);
        }
    }

    /**
     * Decide how a file can be shown: pdf, image, text, or none (download only).
     * Uses the real file content type, not the one the browser reported at upload.
     */
    private function previewKind(StudyFile $studyFile): string
    {
        // Office documents (PowerPoint, Word, Excel) are shown through Office Online
        if ($this->officeMime($studyFile) !== null) {
            return 'office';
        }

        $mime = Storage::disk('local')->mimeType($studyFile->file_path);

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

    /**
     * Returns the MIME type if the file is a PowerPoint, Word or Excel file
     * (decided by file extension), otherwise null.
     */
    private function officeMime(StudyFile $studyFile): ?string
    {
        $extension = strtolower(pathinfo($studyFile->file_name, PATHINFO_EXTENSION));

        return [
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc'  => 'application/msword',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
        ][$extension] ?? null;
    }
}