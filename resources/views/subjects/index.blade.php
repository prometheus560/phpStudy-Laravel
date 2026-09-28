@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">My Subjects</h2>
        <p class="text-gray-400 text-sm mt-1">Manage your subjects and keep track of the tasks assigned to each one.</p>
    </div>

    {{-- Add Subject --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5 mb-6">
        <h3 class="text-lg font-bold text-white">Add New Subject</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Add a subject to start organizing your study tasks.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-400 text-sm rounded-lg px-4 py-2 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('subjects.store') }}" class="flex flex-wrap gap-3">
            @csrf

            <input type="text" name="subject_name" value="{{ old('subject_name') }}"
                   placeholder="Enter subject name" maxlength="100" required
                   class="flex-1 min-w-[220px] border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-3 text-sm">

            <button type="submit"
                    class="bg-blue-700 text-white rounded-lg px-5 py-3 text-sm font-medium hover:bg-blue-800">
                Add Subject
            </button>
        </form>
    </div>

    {{-- Subject List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">Your Subjects</h3>
            <p class="text-gray-400 text-sm mt-1">{{ $subjects->count() }} subject(s) in your planner.</p>
        </div>

        @if ($subjects->count() > 0)

            <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">

                @foreach ($subjects as $subject)

                    <div class="border border-[#23232f] rounded-xl p-5 bg-[#1b1b28]">
                        <h4 class="text-blue-400 font-semibold text-lg mb-2">
                            {{ $subject->subject_name }}
                        </h4>

                        <p class="text-gray-400 text-sm mb-4">
                            {{ $subject->tasks_count }} {{ $subject->tasks_count == 1 ? 'task' : 'tasks' }}
                        </p>

                        <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                              onsubmit="return confirm('Are you sure you want to delete this subject?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No subjects yet</h4>
                <p class="text-gray-400 text-sm mt-1">Add your first subject above to start planning your studies.</p>
            </div>

        @endif

    </div>

</div>

@endsection