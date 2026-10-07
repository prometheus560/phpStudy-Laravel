@extends('layouts.app')

@section('title', 'Sort Subjects')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Sort Subjects</h2>
        <p class="text-gray-400 text-sm mt-1">Your subjects sorted alphabetically using Selection Sort.</p>
    </div>

    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5">

        <h3 class="text-lg font-bold text-white mb-1">Alphabetical Subject Order</h3>
        <p class="text-gray-400 text-sm mb-6">This page uses Selection Sort to arrange your subjects alphabetically.</p>

        @if (count($subjects) > 0)

            @php $number = 1; @endphp

            <div class="flex flex-col gap-2.5">

                @foreach ($subjects as $subject)

                    <div class="flex items-center gap-4 p-4 bg-[#1b1b28] rounded-xl">

                        <div class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold shrink-0">
                            {{ $number }}
                        </div>

                        <div class="font-bold text-white">
                            {{ $subject->subject_name }}
                        </div>

                    </div>

                    @php $number++; @endphp

                @endforeach

            </div>

        @else

            <div class="text-center py-16">
                <h4 class="font-semibold text-white">No subjects found</h4>
                <p class="text-gray-400 text-sm mt-1">You don't have any subjects yet.</p>

                <div class="mt-4">
                    <a href="{{ route('subjects.index') }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Add Subject
                    </a>
                </div>
            </div>

        @endif

    </div>

    {{-- How the sort worked --}}
    @if (count($steps) > 0)
        <details class="mt-6 bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <summary class="cursor-pointer text-sm font-semibold text-blue-400">
                See how Selection Sort did it
            </summary>

            <p class="text-gray-400 text-sm mt-3">
                Each pass looks through the part of the list that isn't sorted yet, finds the
                alphabetically first subject, and moves it to the front of that part.
            </p>

            <div class="mt-4">
                <p class="text-sm font-semibold text-gray-200">Start</p>
                <ol class="mt-1.5 text-sm text-gray-400 list-decimal list-inside">
                    @foreach ($original as $name)
                        <li class="truncate">{{ $name }}</li>
                    @endforeach
                </ol>
            </div>

            <div class="mt-4 flex flex-col gap-4">
                @foreach ($steps as $step)
                    <div>
                        <p class="text-sm font-semibold text-gray-200">
                            Pass {{ $step['number'] }}
                            <span class="font-normal text-gray-400">
                                &middot; picked {{ $step['picked'] }}{{ $step['swapped'] ? '' : ' (already in place)' }}
                            </span>
                        </p>
                        <ol class="mt-1.5 text-sm text-gray-400 list-decimal list-inside">
                            @foreach ($step['order'] as $name)
                                <li class="truncate {{ $loop->iteration <= $step['number'] ? 'text-gray-200' : '' }}">{{ $name }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </details>
    @endif

</div>

@endsection