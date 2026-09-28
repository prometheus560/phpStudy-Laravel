@extends('layouts.app')

@section('title', 'Study Schedule')

@section('content')

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Study Schedule</h2>
        <p class="text-gray-400 text-sm mt-1">Plan your study time and stay organized.</p>
    </div>

    {{-- Add Schedule --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5 mb-6">
        <h3 class="text-lg font-bold text-white">Add Study Schedule</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Create a study session by selecting a subject, date, and time.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-400 text-sm rounded-lg px-4 py-2 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($subjects->count() > 0)

            <form method="POST" action="{{ route('schedule.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf

                {{-- Subject --}}
                <div class="sm:col-span-2">
                    <label for="subject_id" class="block text-sm font-semibold text-gray-200 mb-1.5">Subject</label>
                    <select id="subject_id" name="subject_id" required
                            class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                        <option value="">Select Subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Study Date --}}
                <div>
                    <label for="study_date" class="block text-sm font-semibold text-gray-200 mb-1.5">Study Date</label>
                    <input id="study_date" type="date" name="study_date" value="{{ old('study_date') }}" required
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                </div>

                {{-- Start Time --}}
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-gray-200 mb-1.5">Start Time</label>
                    <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                </div>

                {{-- End Time --}}
                <div>
                    <label for="end_time" class="block text-sm font-semibold text-gray-200 mb-1.5">End Time</label>
                    <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required
                           class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3.5 py-2.5 text-sm">
                </div>

                {{-- Submit --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-blue-700 text-white rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-blue-800">
                        Add Schedule
                    </button>
                </div>

            </form>

        @else

            <div class="text-center py-10">
                <h4 class="font-semibold text-white">No subjects available</h4>
                <p class="text-gray-400 text-sm mt-1">You need to create a subject before creating a schedule.</p>
                <div class="mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Add Subject
                    </a>
                </div>
            </div>

        @endif
    </div>

    {{-- Schedule List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">My Schedule</h3>
            <p class="text-gray-400 text-sm mt-1">
                {{ $schedules->count() }} {{ $schedules->count() == 1 ? 'study session' : 'study sessions' }} planned.
            </p>
        </div>

        @if ($schedules->count() > 0)

            <div class="flex flex-col gap-4">

                @foreach ($schedules as $schedule)

                    <div class="border border-[#23232f] rounded-xl p-5 bg-[#1b1b28]">
                        <div class="flex flex-wrap items-center justify-between gap-5">

                            {{-- Schedule Information --}}
                            <div class="flex-1 min-w-[240px]">
                                <h4 class="text-blue-400 font-semibold text-lg mb-2">
                                    {{ $schedule->subject->subject_name }}
                                </h4>

                                <p class="text-gray-400 text-sm mb-1">
                                    <span class="font-semibold text-gray-300">Date:</span>
                                    {{ $schedule->study_date->format('F d, Y') }}
                                </p>

                                <p class="text-gray-400 text-sm">
                                    <span class="font-semibold text-gray-300">Time:</span>
                                    {{ date('g:i A', strtotime($schedule->start_time)) }}
                                    -
                                    {{ date('g:i A', strtotime($schedule->end_time)) }}
                                </p>
                            </div>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('schedule.destroy', $schedule) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 text-white rounded-lg px-3.5 py-2 text-sm font-medium hover:bg-red-700">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No study schedules yet</h4>
                <p class="text-gray-400 text-sm mt-1">Add your first study session above to start planning your time.</p>
            </div>

        @endif

    </div>

</div>

@endsection