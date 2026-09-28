@extends('layouts.app')

@section('title', 'Find Subject')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Find Subject</h2>
        <p class="text-gray-400 text-sm mt-1">Search for a subject using Binary Search.</p>
    </div>

    {{-- Search --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5 mb-6">
        <h3 class="text-lg font-bold text-white mb-1">Search Your Subject</h3>
        <p class="text-gray-400 text-sm mb-5">Enter the exact subject name you want to find.</p>

        <form method="GET" action="{{ route('binary_search.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Enter subject name" required
                   class="flex-1 min-w-[220px] border border-[#2a2a38] rounded-lg px-3.5 py-3 text-sm bg-[#1b1b28] text-gray-200 placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">

            <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-5 py-3 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">
                Search
            </button>
        </form>

        @if ($message !== '')
            <div class="mt-5 bg-blue-500/10 border border-blue-500/25 text-blue-400 rounded-lg px-4 py-3 font-semibold text-sm">
                {{ $message }}
            </div>
        @endif
    </div>

    {{-- Subject list --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">
        <h3 class="text-lg font-bold text-white mb-1">Your Subjects</h3>
        <p class="text-gray-400 text-sm mb-4">Subjects available for searching.</p>

        @if ($subjects->count() > 0)

            <div class="flex flex-col divide-y divide-[#23232f]">
                @foreach ($subjects as $subject)
                    <div class="py-3.5">
                        <strong class="text-gray-200">{{ $subject->subject_name }}</strong>
                    </div>
                @endforeach
            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No subjects yet</h4>
                <p class="text-gray-400 text-sm mt-1">You don't have any subjects yet.</p>

                <div class="mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="inline-block bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">
                        Add Subject
                    </a>
                </div>
            </div>

        @endif
    </div>

</div>

@endsection