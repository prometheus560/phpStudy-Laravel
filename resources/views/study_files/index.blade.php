@extends('layouts.app')

@section('title', 'My Files')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-8">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">My Files</h2>
        <p class="text-gray-400 text-sm mt-1">Store and manage your study materials.</p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-400 text-sm rounded-lg px-4 py-2 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/25 text-red-400 text-sm rounded-lg px-4 py-2 mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload File --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-[#23232f]">
            <h3 class="font-semibold text-white">Upload Study File</h3>
        </div>

        <div class="p-5">
            <form method="POST" action="{{ route('study_files.store') }}" enctype="multipart/form-data"
                  class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf

                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Subject</label>
                    <select name="subject_id" class="w-full border border-[#2a2a38] rounded-lg px-3 py-2 text-sm bg-[#1b1b28] text-gray-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">No Subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Choose File</label>
                    <input type="file" name="file" required
                           class="w-full border border-[#2a2a38] rounded-lg px-3 py-2 text-sm bg-[#1b1b28] text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#2a2a38] file:text-gray-200 file:text-sm file:font-medium hover:file:bg-blue-600 hover:file:text-white">
                    <p class="text-xs text-gray-500 mt-1">Maximum file size: 10 MB</p>
                </div>

                <div class="md:col-span-2 flex items-end">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Search and Filter --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm mb-6 p-5">
        <form method="GET" action="{{ route('study_files.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">

            <div class="md:col-span-6">
                <label class="block text-sm font-medium text-gray-300 mb-1">Search Files</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by file name..."
                       class="w-full border border-[#2a2a38] rounded-lg px-3 py-2 text-sm bg-[#1b1b28] text-gray-200 placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-300 mb-1">Subject</label>
                <select name="subject_id" class="w-full border border-[#2a2a38] rounded-lg px-3 py-2 text-sm bg-[#1b1b28] text-gray-200 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="">All Subjects</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                            {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-end">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">
                    Search
                </button>
            </div>
        </form>
    </div>

    {{-- File List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @forelse ($files as $file)
            <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm flex flex-col justify-between">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="text-3xl leading-none">📄</div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-white truncate">{{ $file->file_name }}</h4>
                            @if ($file->subject)
                                <span class="inline-block mt-1 bg-blue-500/15 text-blue-400 text-xs rounded-full px-2 py-0.5">
                                    {{ $file->subject->subject_name }}
                                </span>
                            @else
                                <span class="inline-block mt-1 bg-white/5 text-gray-400 text-xs rounded-full px-2 py-0.5">
                                    No Subject
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 space-y-1 text-sm text-gray-400">
                        <p><span class="font-medium text-gray-300">Type:</span> {{ $file->file_type ?? 'Unknown' }}</p>
                        <p>
                            <span class="font-medium text-gray-300">Size:</span>
                            @if ($file->file_size)
                                {{ number_format($file->file_size / 1024, 2) }} KB
                            @else
                                Unknown
                            @endif
                        </p>
                        <p class="text-gray-500">Uploaded: {{ $file->created_at?->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="border-t border-[#23232f] px-5 py-3 flex gap-2">
                    <a href="{{ route('study_files.download', $file) }}"
                       class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium hover:from-blue-500 hover:to-blue-600">
                        Download
                    </a>
                    <form method="POST" action="{{ route('study_files.destroy', $file) }}"
                          onsubmit="return confirm('Delete this file?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="border border-red-500/30 text-red-400 rounded-lg px-3 py-1.5 text-xs font-medium hover:bg-red-500/10">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm text-center py-16">
                <div class="text-5xl mb-3">📁</div>
                <h4 class="font-semibold text-white">No Files Found</h4>
                <p class="text-gray-400 text-sm mt-1">Upload your first study file to get started.</p>
            </div>
        @endforelse

    </div>

</div>

@endsection