@extends('layouts.app')

@section('title', 'My Files')

@section('content')

@php
    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-2.5 text-sm transition';

    // Badge color by file extension
    $tones = [
        'pdf'  => 'bg-red-500/15 text-red-300 border-red-500/25',
        'doc'  => 'bg-blue-500/15 text-blue-300 border-blue-500/25',
        'docx' => 'bg-blue-500/15 text-blue-300 border-blue-500/25',
        'ppt'  => 'bg-orange-500/15 text-orange-300 border-orange-500/25',
        'pptx' => 'bg-orange-500/15 text-orange-300 border-orange-500/25',
        'xls'  => 'bg-green-500/15 text-green-300 border-green-500/25',
        'xlsx' => 'bg-green-500/15 text-green-300 border-green-500/25',
        'csv'  => 'bg-green-500/15 text-green-300 border-green-500/25',
        'png'  => 'bg-purple-500/15 text-purple-300 border-purple-500/25',
        'jpg'  => 'bg-purple-500/15 text-purple-300 border-purple-500/25',
        'jpeg' => 'bg-purple-500/15 text-purple-300 border-purple-500/25',
        'zip'  => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/25',
    ];

    $size = function ($bytes) {
        if (!$bytes) return 'Unknown';
        return $bytes >= 1048576 ? number_format($bytes / 1048576, 1) . ' MB' : number_format($bytes / 1024, 1) . ' KB';
    };

    $filtered = request('search') || request('subject_id');
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">My Files</h2>
            <p class="text-gray-400 text-sm mt-1">Store and manage your study materials.</p>
        </div>
        <span id="live-count" data-unit="file" class="text-xs font-semibold bg-blue-500/10 text-blue-300 border border-blue-500/20 rounded-lg px-3 py-1.5">
            {{ $files->count() }} {{ $files->count() == 1 ? 'file' : 'files' }}
        </span>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-4 py-3 mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-white mb-4">Upload Study File</h3>

        <form method="POST" action="{{ route('study_files.store') }}" enctype="multipart/form-data" id="upload-form" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            @csrf

            <div class="md:col-span-4">
                <label for="up_subject" class="block text-sm font-semibold text-gray-200 mb-1.5">Subject</label>
                <select id="up_subject" name="subject_id" class="{{ $field }}">
                    <option value="">No subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-8">
                <label class="block text-sm font-semibold text-gray-200 mb-1.5">File</label>
                <label for="file" id="drop"
                       class="flex flex-col items-center justify-center text-center cursor-pointer rounded-xl border-2 border-dashed border-[#2a2a38] bg-[#12121c] hover:border-blue-500/50 hover:bg-blue-500/5 px-4 py-6 transition">
                    <svg class="w-7 h-7 text-blue-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span id="drop-text" class="text-sm text-gray-300">Click to choose a file or drag it here</span>
                    <span class="text-xs text-gray-500 mt-1">Maximum file size: 10 MB</span>
                    <input id="file" type="file" name="file" required class="sr-only">
                </label>
                <p id="file-error" class="hidden text-sm text-red-400 mt-2">That file is larger than 10 MB.</p>
            </div>

            <div class="md:col-span-12">
                <button type="submit" id="upload-btn"
                        class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-6 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500 disabled:opacity-70">
                    Upload
                </button>
            </div>
        </form>
    </div>

    {{-- Search and filter --}}
    <form method="GET" action="{{ route('study_files.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" id="live-search" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Type a letter to find a file..." class="flex-1 min-w-[200px] {{ $field }}">

        <select name="subject_id" id="live-subject" class="sm:w-56 {{ $field }}">
            <option value="">All subjects</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
            @endforeach
        </select>

        <button type="button" id="live-clear" class="hidden rounded-xl px-5 py-2.5 text-sm font-medium border border-[#2a2a38] bg-[#14141f] text-gray-300 hover:bg-white/5">Clear</button>
    </form>

    {{-- File list --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @forelse ($files as $file)
            @php
                $ext  = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                $tone = $tones[$ext] ?? 'bg-white/5 text-gray-300 border-white/10';
            @endphp

            <div class="js-item flex flex-col justify-between bg-[#14141f] border border-[#23232f] rounded-2xl hover:border-blue-500/30 transition"
                 data-name="{{ mb_strtolower($file->file_name) }}"
                 data-subject="{{ mb_strtolower($file->subject->subject_name ?? '') }}"
                 data-subject-id="{{ $file->subject_id }}">
                <div class="p-5 flex items-start gap-4">
                    <div class="w-12 h-12 shrink-0 rounded-xl border grid place-items-center text-[11px] font-bold uppercase {{ $tone }}">{{ $ext ?: 'file' }}</div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('study_files.show', $file) }}" class="block">
                            <h4 class="font-semibold text-white truncate hover:text-blue-300">{{ $file->file_name }}</h4>
                        </a>

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if ($file->subject)
                                <span class="bg-blue-500/10 text-blue-300 border border-blue-500/20 text-[11px] font-semibold rounded-md px-2 py-0.5">{{ $file->subject->subject_name }}</span>
                            @else
                                <span class="bg-white/5 text-gray-400 text-[11px] font-semibold rounded-md px-2 py-0.5">No subject</span>
                            @endif
                            <span class="text-xs text-gray-500">{{ $size($file->file_size) }}</span>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">Uploaded {{ $file->created_at?->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="border-t border-[#23232f] px-5 py-3 flex flex-wrap gap-2">
                    <a href="{{ route('study_files.show', $file) }}" class="rounded-lg px-3.5 py-1.5 text-xs font-semibold border border-green-500/40 text-green-400 hover:bg-green-500/10">View</a>
                    <a href="{{ route('study_files.download', $file) }}" class="rounded-lg px-3.5 py-1.5 text-xs font-semibold bg-blue-600 text-white hover:bg-blue-500">Download</a>
                    <form method="POST" action="{{ route('study_files.destroy', $file) }}" onsubmit="return confirm('Delete this file?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg px-3.5 py-1.5 text-xs font-semibold border border-red-500/30 text-red-400 hover:bg-red-500/10">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-[#14141f] border border-[#23232f] rounded-2xl text-center py-16">
                <h4 class="font-semibold text-white">{{ $filtered ? 'No files match your search' : 'No files yet' }}</h4>
                <p class="text-gray-400 text-sm mt-1">{{ $filtered ? 'Try a different search or subject.' : 'Upload your first study file to get started.' }}</p>
            </div>
        @endforelse

        <div id="live-empty" class="hidden md:col-span-2 bg-[#14141f] border border-[#23232f] rounded-2xl text-center py-16">
            <h4 class="font-semibold text-white">No file matches</h4>
            <p class="text-gray-400 text-sm mt-1">Nothing starts with what you typed. Try another letter.</p>
        </div>

    </div>
</div>

<script>
(() => {
    const input = document.getElementById('file');
    const text  = document.getElementById('drop-text');
    const err   = document.getElementById('file-error');
    const form  = document.getElementById('upload-form');
    const btn   = document.getElementById('upload-btn');
    const drop  = document.getElementById('drop');
    const MAX   = 10 * 1024 * 1024;

    function show() {
        const f = input.files[0];
        if (!f) { text.textContent = 'Click to choose a file or drag it here'; err.classList.add('hidden'); return; }
        text.textContent = f.name + ' (' + (f.size / 1024 / 1024).toFixed(2) + ' MB)';
        err.classList.toggle('hidden', f.size <= MAX);
    }
    input.addEventListener('change', show);

    ['dragenter', 'dragover'].forEach(n => drop.addEventListener(n, e => { e.preventDefault(); drop.classList.add('border-blue-500'); }));
    ['dragleave', 'drop'].forEach(n => drop.addEventListener(n, e => { e.preventDefault(); drop.classList.remove('border-blue-500'); }));
    drop.addEventListener('drop', e => {
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; show(); }
    });

    form.addEventListener('submit', e => {
        const f = input.files[0];
        if (f && f.size > MAX) { e.preventDefault(); err.classList.remove('hidden'); return; }
        btn.disabled = true;
        btn.textContent = 'Uploading...';
    });
    window.addEventListener('pageshow', () => { btn.disabled = false; btn.textContent = 'Upload'; });
})();
</script>

<script>
(() => {
    const input  = document.getElementById('live-search');
    const select = document.getElementById('live-subject');
    const clear  = document.getElementById('live-clear');
    const empty  = document.getElementById('live-empty');
    const count  = document.getElementById('live-count');
    const items  = [...document.querySelectorAll('.js-item')];

    if (!input) return;

    function apply() {
        const q = input.value.trim().toLowerCase();
        const subjectId = select ? select.value : '';
        let shown = 0;

        // Look at the items one by one (Linear Search)
        for (let i = 0; i < items.length; i++) {
            const item = items[i];

            const textOk = q === ''
                || item.dataset.name.startsWith(q)
                || item.dataset.subject.startsWith(q);

            const subjectOk = subjectId === '' || item.dataset.subjectId === subjectId;

            const ok = textOk && subjectOk;
            item.classList.toggle('hidden', !ok);
            if (ok) shown++;
        }

        if (empty) empty.classList.toggle('hidden', shown > 0 || items.length === 0);
        if (clear) clear.classList.toggle('hidden', q === '' && subjectId === '');
        if (count && count.dataset.unit) {
            count.textContent = shown + ' ' + count.dataset.unit + (shown === 1 ? '' : 's');
        }
    }

    input.addEventListener('input', apply);
    if (select) select.addEventListener('change', apply);
    if (input.form) input.form.addEventListener('submit', e => e.preventDefault());

    if (clear) {
        clear.addEventListener('click', () => {
            input.value = '';
            if (select) select.value = '';
            apply();
            input.focus();
        });
    }

    apply();
})();
</script>

@endsection