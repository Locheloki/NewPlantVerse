@extends('layouts.app')

@section('page-title', 'My Plants')

@section('main-content')
<div class="space-y-6">
    <!-- Add Button -->
    <div class="flex justify-end">
        <a href="{{ route('plants.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>Add Plant
        </a>
    </div>

    <!-- Plants Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plants as $plant)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <!-- Plant Image -->
            <div class="h-40 bg-gradient-to-br from-green-100 to-green-50 flex items-center justify-center overflow-hidden">
                @if($plant->photo_url)
                <img src="{{ asset('storage/' . $plant->photo_url) }}" alt="{{ $plant->name }}" class="w-full h-full object-cover">
                @else
                <i class="fas fa-leaf text-6xl text-green-300"></i>
                @endif
            </div>

            <!-- Plant Info -->
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800">{{ $plant->name }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ $plant->species }}</p>

                <!-- Care Consistency -->
                <div class="mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Care Consistency</span>
                        <span class="text-sm font-bold text-green-600">{{ $plant->care_consistency }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: '{{ $plant->care_consistency }}%'"></div>
                    </div>
                </div>

                <!-- Status Badge -->
                @if($plant->is_neglected)
                <div class="mb-4 p-2 bg-red-100 border border-red-300 rounded text-red-700 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>This plant needs attention!
                </div>
                @endif

                <!-- View Button -->
                <a href="{{ route('plants.show', $plant->id) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg">
                    View Details
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-leaf text-6xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 text-lg mb-4">No plants yet!</p>
            <a href="{{ route('plants.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                Add Your First Plant
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection