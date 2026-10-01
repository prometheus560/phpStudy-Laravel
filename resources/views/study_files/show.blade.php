@extends('layouts.app')

@section('title', $studyFile->file_name)

@section('content')

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <a href="{{ route('study_files.index') }}" class="text-sm text-gray-400 hover:text-white">&larr; Back to My Files</a>
            <h2 class="text-2xl font-bold text-white mt-2 truncate">{{ $studyFile->file_name }}</h2>

            <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-400">
                @if ($studyFile->subject)
                    <span class="bg-blue-500/15 text-blue-300 text-xs rounded-full px-2 py-0.5">
                        {{ $studyFile->subject->subject_name }}
                    </span>
                @else
                    <span class="bg-white/10 text-gray-400 text-xs rounded-full px-2 py-0.5">No Subject</span>
                @endif

                @if ($studyFile->file_size)
                    <span>{{ number_format($studyFile->file_size / 1024, 2) }} KB</span>
                @endif
            </div>
        </div>

        <a href="{{ route('study_files.download', $studyFile) }}"
           class="bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
            Download
        </a>
    </div>

    {{-- Preview --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-3">

        @if ($kind === 'pdf')

            <iframe src="{{ route('study_files.preview', $studyFile) }}"
                    title="{{ $studyFile->file_name }}"
                    class="w-full h-[80vh] rounded-lg bg-white"></iframe>

        @elseif ($kind === 'text')

            <iframe src="{{ route('study_files.preview', $studyFile) }}"
                    title="{{ $studyFile->file_name }}"
                    class="w-full h-[80vh] rounded-lg bg-white"></iframe>

        @elseif ($kind === 'image')

            <div class="flex justify-center">
                <img src="{{ route('study_files.preview', $studyFile) }}"
                     alt="{{ $studyFile->file_name }}"
                     class="max-w-full max-h-[80vh] rounded-lg">
            </div>

        @else

            <div class="text-center py-20">
                <div class="text-5xl mb-3">📄</div>
                <h4 class="font-semibold text-white">Preview not available</h4>
                <p class="text-gray-400 text-sm mt-1">
                    This file type can't be shown in the browser. You can download it instead.
                </p>
                <div class="mt-4">
                    <a href="{{ route('study_files.download', $studyFile) }}"
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800">
                        Download
                    </a>
                </div>
            </div>

        @endif

    </div>

</div>

@endsection