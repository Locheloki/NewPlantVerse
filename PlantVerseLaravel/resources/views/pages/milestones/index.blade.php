@extends('layouts.app')

@section('page-title', 'Milestones')

@section('main-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">🏆 Your Milestones</h1>
        <p class="text-gray-600">Track your achievements and reach new heights in your plant journey!</p>
    </div>

    <!-- Milestones Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($milestones as $milestone)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 {{ $milestone->is_completed ? 'border-2 border-green-400' : 'border-2 border-gray-200' }}">
            <!-- Header -->
            <div class="bg-gradient-to-r {{ $milestone->is_completed ? 'from-green-400 to-green-600' : 'from-blue-400 to-blue-600' }} p-4">
                <div class="flex items-center justify-between">
                    <div class="text-4xl">
                        @if($milestone->is_completed)
                        🏆
                        @else
                        🎯
                        @endif
                    </div>
                    @if($milestone->is_completed)
                    <span class="px-3 py-1 bg-white text-green-600 text-xs font-bold rounded-full">✓ COMPLETED</span>
                    @else
                    <span class="px-3 py-1 bg-white text-blue-600 text-xs font-bold rounded-full">IN PROGRESS</span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $milestone->title }}</h3>
                <p class="text-sm text-gray-600 mb-6">{{ $milestone->description }}</p>

                <!-- Progress Section -->
                <div class="space-y-3">
                    <!-- Progress Stats -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm font-bold text-gray-800">
                            {{ $milestone->progress }} / {{ $milestone->target }}
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    @php
                    $progressPercentage = (int) min(($milestone->progress / $milestone->target) * 100, 100);
                    $bgColor = $milestone->is_completed ? '#10b981' : '#3b82f6';
                    @endphp
                    <div class="w-full bg-gray-300 rounded-full h-3 overflow-hidden shadow-inner">
                        <div
                            class="h-3 rounded-full transition-all duration-500 font-bold text-white text-xs flex items-center justify-center"
                            style="width: {{ $progressPercentage }}%; background: linear-gradient(90deg, {{ $bgColor }}, {{ $milestone->is_completed ? '#059669' : '#1d4ed8' }});">
                        </div>
                    </div>

                    <!-- Percentage -->
                    <div class="text-right pt-2">
                        <span class="text-2xl font-bold {{ $milestone->is_completed ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $progressPercentage }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <i class="fas fa-trophy text-6xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500 text-lg font-medium mb-2">No milestones yet!</p>
                <p class="text-gray-400">Keep caring for your plants to unlock milestones and achievements.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Summary Stats -->
    @if($milestones->isNotEmpty())
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ $milestones->where('is_completed', true)->count() }}</p>
                </div>
                <div class="text-4xl">✓</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $milestones->where('is_completed', false)->count() }}</p>
                </div>
                <div class="text-4xl">🎯</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Progress</p>
                    @php
                    $totalProgress = $milestones->sum('progress');
                    $totalTarget = $milestones->sum('target');
                    $overallPercentage = $totalTarget > 0 ? round(($totalProgress / $totalTarget) * 100) : 0;
                    @endphp
                    <p class="text-3xl font-bold text-purple-600">{{ $overallPercentage }}%</p>
                </div>
                <div class="text-4xl">🌱</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection