<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SelectionSortController extends Controller
{
    public function index()
    {
        // Get the current user's subjects
        $subjects = Subject::where('user_id', Auth::id())
            ->orderBy('id', 'asc')
            ->get()
            ->all();

        // Selection Sort
        $n = count($subjects);

        for ($i = 0; $i < $n - 1; $i++) {

            $smallest = $i;

            for ($j = $i + 1; $j < $n; $j++) {

                if (
                    strcasecmp(
                        $subjects[$j]->subject_name,
                        $subjects[$smallest]->subject_name
                    ) < 0
                ) {
                    $smallest = $j;
                }
            }

            if ($smallest != $i) {

                $temp = $subjects[$i];

                $subjects[$i] = $subjects[$smallest];

                $subjects[$smallest] = $temp;
            }
        }

        return view('selection_sort.index', compact('subjects'));
    }
}