@extends('layouts.app')

@section('title', 'My Subjects')

@section('content')

@php
    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-3 text-sm transition';
@endphp

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-3xl font-bold text-white tracking-tight">My Subjects</h2>
        <p class="text-gray-400 text-sm mt-1">Manage your subjects and keep track of the tasks assigned to each one.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    {{-- Add Subject --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white">Add New Subject</h3>
        <p class="text-gray-400 text-sm mt-1 mb-5">Add a subject to start organizing your study tasks.</p>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('subjects.store') }}" class="flex flex-wrap gap-3">
            @csrf

            <input type="text" name="subject_name" value="{{ old('subject_name') }}"
                   placeholder="e.g. Web Development" maxlength="100" required
                   class="flex-1 min-w-[220px] {{ $field }}">

            <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-3 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
                + Add Subject
            </button>
        </form>
    </div>

    {{-- Subject List --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6">

        <div class="mb-5">
            <h3 class="text-lg font-bold text-white">Your Subjects</h3>
            <p class="text-gray-400 text-sm mt-1">{{ $subjects->count() }} {{ $subjects->count() == 1 ? 'subject' : 'subjects' }} in your planner.</p>
        </div>

        @if ($subjects->count() > 0)

            <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">

                @foreach ($subjects as $subject)
                    @php
                        $total = $subject->tasks_count;
                        $done  = $subject->tasks()->whereIn('status', ['Completed', 'completed', 'Done', 'done'])->count();
                        $pend  = $total - $done;
                        $pct   = $total > 0 ? round($done / $total * 100) : 0;
                    @endphp

                    <div class="flex flex-col border border-[#23232f] rounded-2xl p-5 bg-[#1b1b28] hover:border-blue-500/30 transition">

                        <div class="flex items-start justify-between gap-3">
                            <h4 class="text-white font-semibold text-lg leading-snug break-words">{{ $subject->subject_name }}</h4>
                            <span class="text-xs font-semibold text-blue-300 bg-blue-500/10 border border-blue-500/20 rounded-md px-2 py-0.5 shrink-0">{{ $pct }}%</span>
                        </div>

                        <p class="text-gray-400 text-sm mt-2">
                            {{ $pend }} pending &middot; {{ $done }} done
                        </p>

                        <div class="h-1.5 rounded-full bg-white/5 overflow-hidden mt-3">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500" style="width: {{ $pct }}%"></div>
                        </div>

                        <div class="flex items-center justify-between gap-2 mt-5 pt-4 border-t border-[#23232f]">
                            <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300">
                                {{ $total }} {{ $total == 1 ? 'task' : 'tasks' }} &rarr;
                            </a>

                            <form method="POST" action="{{ route('subjects.destroy', $subject) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($subject->subject_name) }}?{{ $total > 0 ? ' This will also delete its ' . $total . ' ' . ($total == 1 ? 'task' : 'tasks') . '.' : '' }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg px-3 py-1.5 text-sm font-medium border border-red-500/30 text-red-400 hover:bg-red-500/10">
                                    Delete
                                </button>
                            </form>
                        </div>
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