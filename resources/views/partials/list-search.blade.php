{{--
    SHARED SEARCH BAR for list pages: type a letter, pick a filter, Clear.

    HOW TO USE ON ANY PAGE
    1. Give the container that holds the items an id, for example:  <div id="note-list"> ... </div>
    2. Mark every item inside it:
           data-search-item                       (this element is one item)
           data-name="{{ mb_strtolower($x->title) }}"   (the text to search, lowercase)
           data-filter="{{ $x->subject_id }}"     (optional: the value the dropdown filters by)
       and put  data-search-name  on the element that shows the name (optional: highlights the typed letters).
    3. Put this where the search bar should appear:
           @include('partials.list-search', [
               'placeholder' => 'Type a letter to find a note...',
               'filterAll'   => 'All subjects',
               'filters'     => $subjects->pluck('subject_name', 'id')->all(),   // leave out for no dropdown
               'target'      => '#note-list',
               'mode'        => 'words',      // words (default) | starts | contains
               'emptyText'   => 'No note matches your search.',
           ])
--}}
@php
    $placeholder = $placeholder ?? 'Type a letter to find...';
    $filters     = $filters ?? [];
    $filterAll   = $filterAll ?? 'All';
    $target      = $target ?? '[data-search-list]';
    $mode        = $mode ?? 'words';
    $emptyText   = $emptyText ?? 'Nothing matches your search.';
@endphp

<div class="flex flex-wrap gap-3" data-list-search data-target="{{ $target }}" data-mode="{{ $mode }}" data-empty="{{ $emptyText }}">

    <div class="control flex-1" style="min-width: 220px">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" data-ls-input class="f-input has-icon"
               placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" autocomplete="off">
    </div>

    @if (count($filters) > 0)
        <select data-ls-filter class="f-input" style="width: 200px; flex: none;" aria-label="Filter">
            <option value="">{{ $filterAll }}</option>
            @foreach ($filters as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    @endif

    <button type="button" data-ls-clear class="btn-ghost" style="height: 48px;">Clear</button>
</div>

@once
<style>
    .ls-hl { color: #c4b5fd; background: rgba(139,92,246,.20); border-radius: 4px; }
    @media (max-width: 560px) { [data-ls-filter] { width: 100% !important; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const NON_WORD = /[^\p{L}\p{N}]+/gu;
    const esc = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

    // " pcit 03" style text, so a word start can be found with " " + typed text
    const norm = (s) => ' ' + s.toLowerCase().replace(NON_WORD, ' ').trim();

    function matches(mode, name, q) {
        if (!q) return true;
        if (mode === 'contains') return name.includes(q);
        if (mode === 'starts') return name.startsWith(q);
        const nq = norm(q).trim();
        if (!nq) return name.includes(q);
        return norm(name).includes(' ' + nq);        // a word of the name starts with the typed text
    }

    // Highlight the typed letters inside the name
    function highlight(el, q, mode) {
        if (!el.dataset.full) el.dataset.full = el.textContent;
        const full = el.dataset.full;
        el.textContent = '';
        if (!q) { el.textContent = full; return; }

        const head = mode === 'contains' ? '(' : mode === 'starts' ? '^(' : '(?:^|[^\\p{L}\\p{N}])(';
        const m = new RegExp(head + esc(q) + ')', 'iu').exec(full);
        if (!m) { el.textContent = full; return; }

        const start = m.index + m[0].length - m[1].length;
        const mark = document.createElement('span');
        mark.className = 'ls-hl';
        mark.textContent = full.slice(start, start + m[1].length);
        el.appendChild(document.createTextNode(full.slice(0, start)));
        el.appendChild(mark);
        el.appendChild(document.createTextNode(full.slice(start + m[1].length)));
    }

    document.querySelectorAll('[data-list-search]').forEach((bar) => {
        const input = bar.querySelector('[data-ls-input]');
        const select = bar.querySelector('[data-ls-filter]');
        const clear = bar.querySelector('[data-ls-clear]');
        const list = document.querySelector(bar.dataset.target);
        if (!list) return;

        const mode = bar.dataset.mode || 'words';
        const items = Array.from(list.querySelectorAll('[data-search-item]'));

        // "Nothing found" message, shown when no item matches
        let empty = list.querySelector('[data-search-empty]');
        if (!empty) {
            empty = document.createElement('div');
            empty.setAttribute('data-search-empty', '');
            empty.className = 'text-center py-12';
            empty.style.display = 'none';
            empty.style.gridColumn = '1 / -1';
            empty.innerHTML = '<h4 class="font-semibold text-white">Nothing found</h4>'
                + '<p class="text-sm mt-1" style="color: var(--muted)"></p>';
            empty.querySelector('p').textContent = bar.dataset.empty || '';
            list.appendChild(empty);
        }

        function apply() {
            const q = input.value.trim().toLowerCase();
            const f = select ? select.value : '';
            let shown = 0;

            items.forEach((it) => {
                const name = it.dataset.name || '';
                const values = (it.dataset.filter || '').split('|');
                const ok = matches(mode, name, q) && (!f || values.includes(f));

                it.style.display = ok ? '' : 'none';
                const nameEl = it.querySelector('[data-search-name]');
                if (nameEl) highlight(nameEl, ok ? q : '', mode);
                if (ok) shown++;
            });

            empty.style.display = shown === 0 ? '' : 'none';
        }

        input.addEventListener('input', apply);
        if (select) select.addEventListener('change', apply);

        clear.addEventListener('click', () => {
            input.value = '';
            if (select) select.value = '';
            apply();
            input.focus();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { input.value = ''; apply(); }
        });

        apply();
    });
});
</script>
@endonce