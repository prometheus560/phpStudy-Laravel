<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinarySearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        // Binary search needs the list sorted by name (A-Z)
        $subjects = Auth::user()
            ->subjects()
            ->get()
            ->sortBy(fn ($subject) => mb_strtolower($subject->subject_name))
            ->values();

        $message = '';
        $steps = [];

        if ($search !== '') {
            $target = mb_strtolower($search);

            $low = 0;
            $high = $subjects->count() - 1;
            $foundIndex = null;

            while ($low <= $high) {
                $mid = intdiv($low + $high, 2);
                $midName = mb_strtolower($subjects[$mid]->subject_name);

                // Remember this step (positions start at 1 to read nicely)
                $step = [
                    'low'  => $low + 1,
                    'high' => $high + 1,
                    'mid'  => $mid + 1,
                    'name' => $subjects[$mid]->subject_name,
                ];

                if ($midName === $target) {
                    $step['result'] = 'This is the one.';
                    $steps[] = $step;
                    $foundIndex = $mid;
                    break;
                }

                if ($midName < $target) {
                    $step['result'] = 'Comes before your search, so look in the lower half.';
                    $low = $mid + 1;
                } else {
                    $step['result'] = 'Comes after your search, so look in the upper half.';
                    $high = $mid - 1;
                }

                $steps[] = $step;
            }

            if ($foundIndex !== null) {
                $message = 'Subject found: ' . $subjects[$foundIndex]->subject_name
                    . ' (position ' . ($foundIndex + 1) . ' in the sorted list).';
            } else {
                $message = 'Subject not found.';
            }
        }

        return view('binary_search.index', compact('search', 'message', 'subjects', 'steps'));
    }
}