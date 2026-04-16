@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('main-content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- PVT Balance -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">PVT Balance</p>
                    <p class="text-3xl font-bold text-green-600">{{ $user->pvt_balance }}</p>
                </div>
                <i class="fas fa-wallet text-5xl text-yellow-400 opacity-30"></i>
            </div>
        </div>

        <!-- Care Consistency -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Care Consistency</p>
                    <p class="text-3xl font-bold text-blue-600">{{ (int) ceil($user->on_time_care_percentage) }}%</p>
                </div>
                <i class="fas fa-chart-pie text-5xl text-blue-400 opacity-30"></i>
            </div>
        </div>

        <!-- Total Plants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Plants</p>
                    <p class="text-3xl font-bold text-green-600">{{ count($plants) }}</p>
                </div>
                <i class="fas fa-leaf text-5xl text-green-400 opacity-30"></i>
            </div>
        </div>
    </div>

    <!-- Streak Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Daily Streak -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold">🔥 Daily Streak</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $user->daily_streak }}</p>
                    <p class="text-xs text-gray-600 mt-2">consecutive days of care</p>
                </div>
                <div class="text-6xl opacity-20">🔥</div>
            </div>
        </div>

        <!-- Longest Plant Streak -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-semibold">🌿 Best Plant Streak</p>
                    @php
                    $bestStreak = $plants->max('care_streak') ?? 0;
                    $bestStreakPlant = $plants->firstWhere('care_streak', $bestStreak);
                    @endphp
                    <p class="text-3xl font-bold text-green-600">{{ $bestStreak }}</p>
                    <p class="text-xs text-gray-600 mt-2">
                        @if($bestStreakPlant)
                        {{ $bestStreakPlant->name }}
                        @else
                        Start caring for plants!
                        @endif
                    </p>
                </div>
                <div class="text-6xl opacity-20">🌿</div>
            </div>
        </div>
    </div>

    <!-- Plant Health Overview with Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between mb-6 gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800">Plant Health Overview</h3>
                <p class="text-sm text-gray-600 mt-1">Care consistency based on task completion</p>
            </div>
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded text-sm text-blue-700 min-w-max">
                <p class="text-xs font-semibold text-blue-900 mb-1">💡 Approximate Health</p>
                <p class="text-xs">Based on on-time care task completion</p>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($plants as $plant)
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium text-gray-700 truncate">{{ $plant->name }}</span>
                        @if($plant->care_streak > 0)
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs rounded whitespace-nowrap font-semibold">🔥 {{ $plant->care_streak }}</span>
                        @endif
                        @if($plant->is_neglected)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded whitespace-nowrap">⚠️ Neglected</span>
                        @endif
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2.5 overflow-hidden">
                        @php
                        $consistency = (int) $plant->care_consistency;
                        if ($consistency >= 85) {
                        $bgColor = '#10b981';
                        } elseif ($consistency >= 60) {
                        $bgColor = '#f59e0b';
                        } else {
                        $bgColor = '#ef4444';
                        }
                        @endphp
                        <div class="h-2.5 rounded-full transition-all duration-500" style="--consistency: {{ $consistency }}%; --bg-color: {{ $bgColor }}; width: var(--consistency); background-color: var(--bg-color);"></div>
                    </div>
                </div>
                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap min-w-fit">{{ $consistency }}%</span>
            </div>
            @empty
            <p class="text-gray-500">No plants yet. <a href="{{ route('plants.create') }}" class="text-green-600 hover:underline">Add one now!</a></p>
            @endforelse
        </div>
    </div>

    <!-- Upcoming Care Tasks -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Care Tasks</h3>
        <div class="space-y-3">
            @forelse($upcomingTasks as $item)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg {{ $item['isOverdue'] ? 'bg-red-50' : '' }}">
                <div class="flex items-center flex-1">
                    <span class="text-2xl mr-3">
                        @switch($item['task']->type)
                        @case('Water')
                        💧
                        @break
                        @case('Sunlight')
                        ☀️
                        @break
                        @case('Fertilize')
                        🌱
                        @break
                        @endswitch
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">{{ $item['plant']->name }}</p>
                        <p class="text-sm text-gray-600">{{ $item['task']->type }}</p>
                    </div>
                </div>
                <div class="text-right mr-4">
                    @if($item['isOverdue'])
                    <span class="text-red-600 font-semibold">OVERDUE</span>
                    @elseif($item['daysUntilDue'] > 0)
                    <span class="text-gray-600 text-sm">{{ (int) ceil($item['daysUntilDue']) }} days</span>
                    @else
                    <span class="text-green-600 text-sm">Due today</span>
                    @endif
                </div>
                <form action="{{ route('plants.log-care', [$item['plant']->id, $item['task']->type]) }}" method="POST" class="inline">
                    @csrf
                    @if($item['isOverdue'] || $item['daysUntilDue'] <= 0)
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-check mr-1"></i>Done
                        </button>
                        @else
                        <button type="button" disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed" title="Available in {{ (int) ceil($item['daysUntilDue']) }} day(s)">
                            <i class="fas fa-clock mr-1"></i>Wait
                        </button>
                        @endif
                </form>
            </div>
            @empty
            <p class="text-gray-500">No upcoming tasks</p>
            @endforelse
        </div>
    </div>
</div>
@endsection nm