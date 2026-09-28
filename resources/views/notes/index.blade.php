@extends('layouts.app')

@section('title', 'My Notes')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">My Notes</h2>
            <p class="text-gray-400 text-sm mt-1">Keep track of everything you're studying.</p>
        </div>
        <button onclick="document.getElementById('newNoteModal').classList.remove('hidden')"
                class="bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
            + New Note
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-400 text-sm rounded-lg px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search + subject filter --}}
    <form method="GET" action="{{ route('notes.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notes..."
               class="border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm flex-1 min-w-[200px]">

        <select name="subject_id" class="border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">
            <option value="">All subjects</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                    {{ $subject->subject_name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-4 py-2 text-sm hover:bg-white/5">
            Filter
        </button>

        @if (request('search') || request('subject_id'))
            <a href="{{ route('notes.index') }}" class="text-sm text-gray-400 hover:underline self-center">
                Clear
            </a>
        @endif
    </form>

    {{-- Notes grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($notes as $note)
            <div class="bg-[#1b1b28] border border-[#23232f] rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-2 gap-2">
                        <h3 class="font-semibold text-white">{{ $note->title }}</h3>
                        <div class="flex gap-2 text-xs shrink-0">
                            <button onclick="document.getElementById('editNoteModal{{ $note->id }}').classList.remove('hidden')"
                                    class="text-blue-400 hover:underline">Edit</button>
                            <form action="{{ route('notes.destroy', $note) }}" method="POST"
                                  onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>

                    @if ($note->subject)
                        <span class="inline-block bg-blue-500/15 text-blue-300 text-xs rounded-full px-2 py-0.5 mb-2">
                            {{ $note->subject->subject_name }}
                        </span>
                    @endif

                    <p class="text-gray-400 text-sm whitespace-pre-line line-clamp-5">{{ $note->content }}</p>
                </div>
                <p class="text-xs text-gray-400 mt-3">Updated {{ $note->updated_at->diffForHumans() }}</p>
            </div>

            {{-- Edit modal for this note --}}
            <div id="editNoteModal{{ $note->id }}" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-[#1b1b28] rounded-xl p-6 w-full max-w-md">
                    <h3 class="font-semibold text-lg mb-4 text-white">Edit Note</h3>
                    <form action="{{ route('notes.update', $note) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="title" value="{{ $note->title }}" required
                               class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">

                        <select name="subject_id" class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">
                            <option value="">No subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected($note->subject_id == $subject->id)>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>

                        <textarea name="content" rows="5" required
                                  class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">{{ $note->content }}</textarea>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button"
                                    onclick="document.getElementById('editNoteModal{{ $note->id }}').classList.add('hidden')"
                                    class="px-4 py-2 text-sm rounded-lg border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-blue-700 text-white">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-[#1b1b28] border border-[#23232f] rounded-xl">
                <p class="text-white font-semibold mb-1">
                    {{ request('search') || request('subject_id') ? 'No notes match your search' : 'No notes yet' }}
                </p>
                <p class="text-gray-400 text-sm mb-4">
                    {{ request('search') || request('subject_id') ? 'Try a different search or filter.' : 'Start by adding your first note.' }}
                </p>
                @unless (request('search') || request('subject_id'))
                    <button onclick="document.getElementById('newNoteModal').classList.remove('hidden')"
                            class="bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        + New Note
                    </button>
                @endunless
            </div>
        @endforelse
    </div>

    {{-- New note modal --}}
    <div id="newNoteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-[#1b1b28] rounded-xl p-6 w-full max-w-md">
            <h3 class="font-semibold text-lg mb-4 text-white">New Note</h3>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/25 text-red-400 text-sm rounded-lg px-3 py-2 mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('notes.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title" required
                       class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">

                <select name="subject_id" class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm">
                    <option value="">No subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>

                <textarea name="content" rows="5" placeholder="Write your note..." required
                          class="w-full border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 rounded-lg px-3 py-2 text-sm"></textarea>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('newNoteModal').classList.add('hidden')"
                            class="px-4 py-2 text-sm rounded-lg border border-[#2a2a38] bg-[#1b1b28] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-blue-700 text-white">Save Note</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any() && !$errors->has('search'))
        <script>document.getElementById('newNoteModal').classList.remove('hidden');</script>
    @endif

@endsection