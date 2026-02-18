@extends('layouts.app')

@section('page-title', 'Milestones')

@section('main-content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($milestones as $milestone)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start">
                    @if($milestone->is_completed)
                    <div class="text-3xl mr-4">🏆</div>
                    @else
                    <div class="text-3xl mr-4">🎯</div>
                    @endif
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $milestone->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $milestone->description }}</p>
                    </div>
                </div>
                @if($milestone->is_completed)
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Completed</span>
                @endif
            </div>

            <!-- Progress -->
            <div class="mt-6">
                <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-bold text-green-600">{{ $milestone->progress }}/{{ $milestone->target }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all" style="width: '{{ min(($milestone->progress / $milestone->target) * 100, 100) }}%'"></div>
                </div>
            </div>
        </div>

        <!-- Percentage -->
        <div class="mt-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ round(($milestone->progress / $milestone->target) * 100) }}%</p>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12">
        <i class="fas fa-trophy text-6xl text-gray-300 mb-4 block"></i>
        <p class="text-gray-500 text-lg">No milestones yet!</p>
    </div>
    @endforelse
</div>
</div>
@endsection