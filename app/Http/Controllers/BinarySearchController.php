<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinarySearchController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Get the user's subjects in alphabetical order
        $subjects = Subject::where('user_id', $userId)
            ->orderBy('subject_name', 'asc')
            ->get();

        $search = '';
        $message = '';

        // Binary Search
        if ($request->has('search')) {

            $search = trim($request->input('search'));

            $left = 0;
            $right = $subjects->count() - 1;

            $found = false;

            while ($left <= $right) {

                $middle = (int) (($left + $right) / 2);

                $name = $subjects[$middle]->subject_name;

                $compare = strcasecmp($name, $search);

                if ($compare === 0) {

                    $message = 'Subject found: ' . $name;

                    $found = true;

                    break;

                } elseif ($compare < 0) {

                    $left = $middle + 1;

                } else {

                    $right = $middle - 1;
                }
            }

            if (!$found) {
                $message = 'Subject not found.';
            }
        }

        return view('binary_search.index', compact(
            'subjects',
            'search',
            'message'
        ));
    }
}