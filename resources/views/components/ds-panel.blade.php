@props(['title', 'rule', 'desc', 'complexity', 'trace' => []])

<div class="bg-[#14141f] border border-blue-500/25 rounded-2xl p-6 mb-6">

    <div class="flex flex-wrap items-center gap-3 mb-2">
        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-300 bg-blue-500/10 border border-blue-500/25 rounded-md px-2 py-1">Data structure</span>
        <h3 class="text-lg font-bold text-white">{{ $title }}</h3>
        <span class="text-xs font-semibold text-gray-400">{{ $rule }}</span>
    </div>

    <p class="text-sm text-gray-400 mb-5">{{ $desc }}</p>

    <div class="rounded-xl bg-[#0f0f1a] border border-[#23232f] p-4 overflow-x-auto">
        {{ $slot }}
    </div>

    <p class="text-xs text-gray-500 mt-4"><span class="font-semibold text-gray-400">Time:</span> {{ $complexity }}</p>

    @if (count($trace) > 0)
        <details class="mt-4 group">
            <summary class="cursor-pointer text-sm font-medium text-blue-400 hover:text-blue-300">Show step-by-step operations ({{ count($trace) }})</summary>
            <ol class="mt-3 max-h-64 overflow-y-auto space-y-1 text-xs font-mono">
                @foreach ($trace as $i => $step)
                    <li class="flex gap-3 text-gray-400">
                        <span class="w-6 text-right text-gray-600">{{ $i + 1 }}.</span>
                        <span class="w-16 font-semibold {{ str_starts_with($step['op'], 'en') || $step['op'] === 'push' ? 'text-green-400' : 'text-amber-400' }}">{{ $step['op'] }}</span>
                        <span class="flex-1 truncate text-gray-300">{{ $step['item'] }}@isset($step['priority']) <span class="text-gray-500">[{{ $step['priority'] }}]</span>@endisset</span>
                        <span class="text-gray-600">size {{ $step['size'] }}</span>
                    </li>
                @endforeach
            </ol>
        </details>
    @endif
</div>