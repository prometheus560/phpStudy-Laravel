@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-white">Good Evening, {{ auth()->user()->name ?? 'Aditya' }}!</h2>
            <p class="text-gray-400 text-sm mt-1">Here is today's overview</p>
        </div>
        <div class="flex gap-3">
            <button class="border border-[#2a2a38] bg-[#14141f] rounded-lg px-4 py-2 text-sm text-gray-300 hover:bg-white/5">⤓ Import</button>
            <button class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">+ New Project</button>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['label' => 'Active projects', 'value' => $stats['active_projects'] ?? 4, 'icon' => '📄'],
            ['label' => 'Priority tasks', 'value' => $stats['priority_tasks'] ?? 6, 'icon' => '📋'],
            ['label' => 'Challenges', 'value' => $stats['challenges'] ?? 2, 'icon' => '⚠️'],
            ['label' => 'Members online', 'value' => $stats['members_online'] ?? 5, 'icon' => '✅'],
        ] as $card)
        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ $card['label'] }}</p>
                <p class="text-3xl font-bold text-white">{{ $card['value'] }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-lg">{{ $card['icon'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        {{-- Progress report chart --}}
        <div class="col-span-2 bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-white">Progress report</h3>
                <button class="text-xs border border-[#2a2a38] rounded-lg px-3 py-1.5 text-gray-400">📅 May - Aug</button>
            </div>
            <canvas id="progressChart" height="140"></canvas>
        </div>

        {{-- Recent activity --}}
        <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-white">Recent activity</h3>
                <a href="#" class="text-xs text-gray-400 border border-[#2a2a38] rounded-lg px-3 py-1 hover:text-white hover:border-blue-500/40">View All</a>
            </div>
            <ul class="space-y-4">
                @foreach ($activities ?? [
                    ['time' => 'Now', 'user' => 'Sarah', 'action' => 'Completed Homepage Wireframe'],
                    ['time' => '2h ago', 'user' => 'James', 'action' => 'Updated The User Profile Design'],
                    ['time' => '3:45', 'user' => 'Laura', 'action' => 'Finalized Color Pallete'],
                    ['time' => '14:40', 'user' => 'David', 'action' => 'Shared The Mobile App Prototype'],
                    ['time' => '11:30', 'user' => 'Chris', 'action' => 'Conducted A Design Critique Session'],
                ] as $item)
                <li class="flex items-start justify-between text-sm">
                    <div class="flex gap-3">
                        <span class="text-gray-500 w-12 shrink-0">{{ $item['time'] }}</span>
                        <span class="text-gray-300"><span class="font-semibold text-white">{{ $item['user'] }},</span> {{ $item['action'] }}</span>
                    </div>
                    <button class="text-gray-600 hover:text-white">×</button>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Projects table --}}
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl p-5">
        <div class="flex items-center justify-between mb-4 gap-3">
            <input type="text" placeholder="Search projects..." class="bg-[#1b1b28] border border-[#2a2a38] rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 flex-1 max-w-xs focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            <div class="flex gap-2">
                <button class="border border-[#2a2a38] rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-white/5">▾ All status</button>
                <button class="border border-[#2a2a38] rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-white/5">▾ More</button>
                <button class="border border-[#2a2a38] rounded-lg px-3 py-2 text-sm text-gray-400 hover:bg-white/5">⤓ Export</button>
                <button class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-3 py-2 text-sm font-medium hover:from-blue-500 hover:to-blue-600 shadow-lg shadow-blue-900/30">+ Add Project</button>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 border-b border-[#23232f]">
                    <th class="text-left font-normal py-3"><input type="checkbox"></th>
                    <th class="text-left font-normal py-3">Project name</th>
                    <th class="text-left font-normal py-3">Creator</th>
                    <th class="text-left font-normal py-3">Status</th>
                    <th class="text-left font-normal py-3">Category</th>
                    <th class="text-left font-normal py-3">Date added</th>
                    <th class="text-left font-normal py-3">Recent updates</th>
                    <th class="text-left font-normal py-3">Manage</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects ?? [
                    ['name' => 'Project Hero', 'creator' => 'James', 'status' => 'Active', 'category' => 'Projects', 'date' => '22 Aug 2025', 'update' => 'Phase two work started.'],
                    ['name' => 'Website revamp', 'creator' => 'Sarah', 'status' => 'Editing', 'category' => 'Websites', 'date' => '15 Aug 2025', 'update' => 'Homepage layout under review.'],
                    ['name' => 'Project Hero', 'creator' => 'Chris', 'status' => 'Active', 'category' => 'Projects', 'date' => '12 Aug 2025', 'update' => 'Requirements list submitted.'],
                ] as $p)
                <tr class="border-b border-[#1e1e2a] hover:bg-white/5">
                    <td class="py-3"><input type="checkbox"></td>
                    <td class="py-3 font-medium text-white">{{ $p['name'] }}</td>
                    <td class="py-3 flex items-center gap-2 text-gray-300">
                        <span class="w-5 h-5 rounded-full bg-gray-700 inline-block"></span>{{ $p['creator'] }}
                    </td>
                    <td class="py-3">
                        <span class="inline-flex items-center gap-1.5 bg-white/5 rounded-full px-2.5 py-1 text-xs text-gray-300">
                            <span class="w-1.5 h-1.5 rounded-full {{ $p['status'] === 'Active' ? 'bg-blue-400' : 'bg-yellow-400' }}"></span>
                            {{ $p['status'] }}
                        </span>
                    </td>
                    <td class="py-3"><span class="bg-white/5 rounded-full px-2.5 py-1 text-xs text-gray-300">{{ $p['category'] }}</span></td>
                    <td class="py-3 text-gray-400">{{ $p['date'] }}</td>
                    <td class="py-3 text-gray-400">{{ $p['update'] }}</td>
                    <td class="py-3"><button class="border border-[#2a2a38] rounded-lg px-3 py-1 text-xs text-gray-300 hover:bg-white/5">Manage</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        const ctx = document.getElementById('progressChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['May', 'June', 'July', 'Aug'],
                datasets: [{
                    data: {{ Js::from($chartData ?? [120, 90, 340, 60]) }},
                    borderColor: '#3b82f6',
                    borderDash: [4, 4],
                    tension: 0.4,
                    pointRadius: 0,
                    fill: false,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#23232f' }, ticks: { color: '#6b7080' } },
                    x: { grid: { display: false }, ticks: { color: '#6b7080' } }
                }
            }
        });
    </script>
@endsection