@extends('layouts.app')

@section('title', $studyFile->file_name)

@section('content')

@php
    $size = $studyFile->file_size
        ? ($studyFile->file_size >= 1048576
            ? number_format($studyFile->file_size / 1048576, 1) . ' MB'
            : number_format($studyFile->file_size / 1024, 1) . ' KB')
        : null;
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <a href="{{ route('study_files.index') }}" class="text-sm text-gray-400 hover:text-white">&larr; Back to My Files</a>
            <h2 class="page-title mt-2 truncate" style="font-size:26px">{{ $studyFile->file_name }}</h2>

            <div class="flex flex-wrap items-center gap-2 mt-3">
                @if ($studyFile->subject)
                    <span class="chip chip-blue">{{ $studyFile->subject->subject_name }}</span>
                @else
                    <span class="chip chip-muted">No subject</span>
                @endif

                @if ($size)
                    <span class="text-sm text-gray-400">{{ $size }}</span>
                @endif

                @if ($studyFile->created_at)
                    <span class="text-sm text-gray-500">&middot; Uploaded {{ $studyFile->created_at->format('M d, Y') }}</span>
                @endif
            </div>
        </div>

        <a href="{{ route('study_files.download', $studyFile) }}" class="btn-primary" style="height:42px;font-size:14px">
            Download
        </a>
    </div>

    {{-- Preview --}}
    <div class="glass p-3">

        @if ($kind === 'pdf' || $kind === 'text')

            <iframe src="{{ route('study_files.preview', $studyFile) }}"
                    title="{{ $studyFile->file_name }}"
                    class="w-full h-[80vh] rounded-xl bg-white"></iframe>

        @elseif ($kind === 'image')

            <div class="flex justify-center">
                <img src="{{ route('study_files.preview', $studyFile) }}"
                     alt="{{ $studyFile->file_name }}"
                     class="max-w-full max-h-[80vh] rounded-xl">
            </div>

        @elseif ($kind === 'office' && $officeUrl)

            <iframe src="{{ $officeUrl }}"
                    title="{{ $studyFile->file_name }}"
                    class="w-full h-[80vh] rounded-xl bg-white"></iframe>

            <p class="text-xs text-gray-500 mt-3 px-1">
                This preview is shown by Microsoft Office Online. If it doesn't load, download the file instead.
            </p>

        @else

            <div class="text-center py-20">
                <h4 class="font-semibold text-white text-lg">Preview not available</h4>
                <p class="text-gray-400 text-sm mt-1">This file type can't be shown in the browser. You can download it instead.</p>
                <a href="{{ route('study_files.download', $studyFile) }}" class="btn-primary mt-5" style="height:42px;font-size:14px">Download</a>
            </div>

        @endif

    </div>

</div>

@endsection