@extends('layouts.app')

@section('title', 'My Notes')

@section('content')

@php
    $field = 'w-full border border-[#2a2a38] bg-[#12121c] text-gray-200 placeholder-gray-500 [color-scheme:dark] focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 rounded-xl px-3.5 py-2.5 text-sm transition';
    $filtered = request('search') || request('subject_id');
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">

    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-3xl font-bold text-white tracking-tight">My Notes</h2>
            <p class="text-gray-400 text-sm mt-1">Keep track of everything you're studying.</p>
        </div>
        <button type="button" data-open="newNoteModal"
                class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg shadow-blue-900/30 hover:from-blue-500 hover:to-indigo-500">
            + New Note
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/25 text-green-300 text-sm rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    {{-- Search + subject filter --}}
    <form method="GET" action="{{ route('notes.index') }}" class="flex flex-wrap gap-3 mb-6">
        <input type="text" id="live-search" name="search" value="{{ request('search') }}" autocomplete="off" placeholder="Type a letter to find a note..."
               class="flex-1 min-w-[200px] {{ $field }}">

        <select name="subject_id" id="live-subject" class="sm:w-56 {{ $field }}">
            <option value="">All subjects</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->subject_name }}</option>
            @endforeach
        </select>

        <button type="button" id="live-clear" class="hidden rounded-xl px-5 py-2.5 text-sm font-medium border border-[#2a2a38] bg-[#14141f] text-gray-300 hover:bg-white/5">Clear</button>
    </form>

    {{-- Notes grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($notes as $note)
            <article class="js-item flex flex-col justify-between bg-[#14141f] border border-[#23232f] rounded-2xl p-5 hover:border-blue-500/30 transition"
                     data-name="{{ mb_strtolower($note->title) }}"
                     data-subject="{{ mb_strtolower($note->subject->subject_name ?? '') }}"
                     data-subject-id="{{ $note->subject_id }}">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="font-semibold text-white leading-snug break-words">{{ $note->title }}</h3>
                        <div class="flex gap-3 text-xs font-medium shrink-0">
                            <button type="button" data-open="editNoteModal{{ $note->id }}" class="text-blue-400 hover:text-blue-300">Edit</button>
                            <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Delete this note?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                            </form>
                        </div>
                    </div>

                    @if ($note->subject)
                        <span class="inline-block bg-blue-500/10 text-blue-300 border border-blue-500/20 text-[11px] font-semibold rounded-md px-2 py-0.5 mb-3">{{ $note->subject->subject_name }}</span>
                    @endif

                    <p class="text-gray-400 text-sm whitespace-pre-line line-clamp-5">{{ $note->content }}</p>
                </div>
                <p class="text-xs text-gray-500 mt-4 pt-3 border-t border-[#23232f]">Updated {{ $note->updated_at->diffForHumans() }}</p>
            </article>

            {{-- Edit modal --}}
            <div id="editNoteModal{{ $note->id }}" data-modal class="hidden fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
                <div class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 w-full max-w-md">
                    <h3 class="font-bold text-lg mb-4 text-white">Edit Note</h3>
                    <form action="{{ route('notes.update', $note) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="title" value="{{ $note->title }}" required class="{{ $field }}">

                        <select name="subject_id" class="{{ $field }}">
                            <option value="">No subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected($note->subject_id == $subject->id)>{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>

                        <textarea name="content" rows="6" required class="{{ $field }}">{{ $note->content }}</textarea>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" data-close class="px-4 py-2 text-sm rounded-xl border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-[#14141f] border border-[#23232f] rounded-2xl">
                <p class="text-white font-semibold mb-1">{{ $filtered ? 'No notes match your search' : 'No notes yet' }}</p>
                <p class="text-gray-400 text-sm mb-4">{{ $filtered ? 'Try a different search or filter.' : 'Start by adding your first note.' }}</p>
                @unless ($filtered)
                    <button type="button" data-open="newNoteModal" class="bg-blue-600 text-white rounded-xl px-5 py-2 text-sm font-semibold hover:bg-blue-500">+ New Note</button>
                @endunless
            </div>
        @endforelse

        <div id="live-empty" class="hidden col-span-full text-center py-16 bg-[#14141f] border border-[#23232f] rounded-2xl">
            <p class="text-white font-semibold mb-1">No note matches</p>
            <p class="text-gray-400 text-sm">Nothing starts with what you typed. Try another letter.</p>
        </div>
    </div>

    {{-- New note modal --}}
    <div id="newNoteModal" data-modal class="hidden fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
        <div class="bg-[#14141f] border border-[#2a2a38] rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-bold text-lg mb-4 text-white">New Note</h3>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/25 text-red-300 text-sm rounded-xl px-3 py-2 mb-3">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('notes.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="title" placeholder="Title" required class="{{ $field }}">

                <select name="subject_id" class="{{ $field }}">
                    <option value="">No subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>

                <textarea name="content" rows="6" placeholder="Write your note..." required class="{{ $field }}"></textarea>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" data-close class="px-4 py-2 text-sm rounded-xl border border-[#2a2a38] text-gray-300 hover:bg-white/5">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const close = m => m.classList.add('hidden');

    document.querySelectorAll('[data-open]').forEach(b =>
        b.addEventListener('click', () => document.getElementById(b.dataset.open).classList.remove('hidden')));

    document.querySelectorAll('[data-modal]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) close(m); });
        m.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => close(m)));
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('[data-modal]').forEach(close);
    });

    @if ($errors->any() && !$errors->has('search'))
        document.getElementById('newNoteModal').classList.remove('hidden');
    @endif
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