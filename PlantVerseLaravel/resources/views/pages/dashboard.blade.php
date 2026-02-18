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
                    <p class="text-3xl font-bold text-blue-600">{{ ceil($user->on_time_care_percentage) }}%</p>
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

    <!-- Plant Health Overview -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Plant Health Overview</h3>
        <div class="space-y-3">
            @forelse($plants as $plant)
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <span class="font-medium text-gray-700">{{ $plant->name }}</span>
                    @if($plant->is_neglected)
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs rounded">Neglected</span>
                    @endif
                </div>
                <div class="w-32 bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: '{{ $plant->care_consistency_int }}%'"></div>
                </div>
                <span class="ml-2 text-sm text-gray-600">{{ $plant->care_consistency_int }}%</span>
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
                    <span class="text-gray-600 text-sm">{{ ceil($item['daysUntilDue']) }} days</span>
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
                        <button type="button" disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed" title="Available in {{ ceil($item['daysUntilDue']) }} day(s)">
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
@endsection