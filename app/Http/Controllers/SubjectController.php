<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $hasCategory = Subject::hasCategory();
        $category    = $hasCategory ? (string) $request->query('category', '') : '';

        $query = auth()->user()
            ->subjects()
            ->withCount('tasks')
            ->orderBy('subject_name');

        // How many subjects are in each category (for the filter buttons)
        $categoryCounts = [];

        if ($hasCategory) {
            $categoryCounts = auth()->user()
                ->subjects()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->all();

            if (in_array($category, Subject::CATEGORIES, true)) {
                $query->where('category', $category);
            } else {
                $category = '';
            }
        }

        $subjects = $query->get();

        return view('subjects.index', [
            'subjects'       => $subjects,
            'hasCategory'    => $hasCategory,
            'category'       => $category,
            'categories'     => Subject::CATEGORIES,
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:100'],
            'category'     => ['nullable', Rule::in(Subject::CATEGORIES)],
        ]);

        $data = ['subject_name' => $validated['subject_name']];

        if (Subject::hasCategory()) {
            $data['category'] = $validated['category'] ?? 'General';
        }

        auth()->user()->subjects()->create($data);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject added.');
    }

    public function update(Request $request, Subject $subject)
    {
        if ($subject->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:100'],
            'category'     => ['nullable', Rule::in(Subject::CATEGORIES)],
        ]);

        $data = ['subject_name' => $validated['subject_name']];

        if (Subject::hasCategory()) {
            $data['category'] = $validated['category'] ?? 'General';
        }

        $subject->update($data);

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->user_id !== auth()->id()) {
            abort(403);
        }

        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted.');
    }
}