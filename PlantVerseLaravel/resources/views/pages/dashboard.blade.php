@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('main-content')
<div class="space-y-5 sm:space-y-8">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('plants.index', ['care_filter' => 'water']) }}" class="block">
            <div class="rounded-lg border-t-4 border-blue-500 bg-white p-4 shadow sm:p-6 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Today's Watering</p>
                        <p class="mt-2 text-2xl font-bold text-blue-600 sm:text-3xl">{{ $todayCounts['water'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $todayCounts['water'] > 0 ? $todayCounts['water'] . ' plants need watering' : 'No plants need watering today!' }}</p>
                    </div>
                    <i class="fas fa-droplet text-3xl text-blue-200 opacity-20 sm:text-4xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('plants.index', ['care_filter' => 'sunlight']) }}" class="block">
            <div class="rounded-lg border-t-4 border-yellow-500 bg-white p-4 shadow sm:p-6 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Today's Sunlight</p>
                        <p class="mt-2 text-2xl font-bold text-yellow-600 sm:text-3xl">{{ $todayCounts['sunlight'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $todayCounts['sunlight'] > 0 ? $todayCounts['sunlight'] . ' plants need sunlight' : 'No plants need sunlight today!' }}</p>
                    </div>
                    <i class="fas fa-sun text-3xl text-yellow-200 opacity-20 sm:text-4xl"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('plants.index', ['care_filter' => 'fertilize']) }}" class="block">
            <div class="rounded-lg border-t-4 border-green-500 bg-white p-4 shadow sm:p-6 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Today's Fertilizer</p>
                        <p class="mt-2 text-2xl font-bold text-green-600 sm:text-3xl">{{ $todayCounts['fertilize'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $todayCounts['fertilize'] > 0 ? $todayCounts['fertilize'] . ' plants need fertilizer' : 'No plants need fertilizer today!' }}</p>
                    </div>
                    <i class="fas fa-seedling text-3xl text-green-200 opacity-20 sm:text-4xl"></i>
                </div>
            </div>
        </a>

        <div class="rounded-lg border-t-4 border-gray-500 bg-white p-4 shadow sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">Total Plants</p>
                    <p class="mt-2 text-2xl font-bold text-gray-700 sm:text-3xl">{{ count($plants) }}</p>
                </div>
                <i class="fas fa-leaf text-3xl text-gray-300 opacity-20 sm:text-4xl"></i>
            </div>
        </div>

        <div class="rounded-lg border-t-4 border-emerald-500 bg-white p-4 shadow sm:col-span-2 sm:p-6 lg:col-span-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">Average Care</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600 sm:text-3xl">{{ $averageCareConsistency }}%</p>
                    <p class="text-xs text-gray-500 mt-1">Across all plants</p>
                </div>
                <i class="fas fa-heart-pulse text-3xl text-emerald-200 opacity-30 sm:text-4xl"></i>
            </div>
        </div>
    </div>

    @if(count($favoritePlants) > 0)
    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <div class="mb-5 sm:mb-6">
            <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">
                <i class="fas fa-star text-yellow-400 mr-2"></i>Favorite Plants
            </h2>
            <p class="text-sm text-gray-600 mt-1">Your pinned plants</p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($favoritePlants as $plant)
            <a href="{{ route('plants.show', $plant->id) }}" class="group favorite-plant-card">
                <div class="flex h-full cursor-pointer flex-col overflow-hidden rounded-lg border-2 border-yellow-400 bg-gradient-to-br from-yellow-50 to-green-50 transition-all duration-200 hover:shadow-lg">
                    <div class="relative h-40 overflow-hidden bg-gray-200">
                        @if($plant->photo_url)
                        <img src="{{ asset('storage/' . $plant->photo_url) }}" alt="{{ $plant->name }}" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
                        @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-green-200 to-blue-200">
                            <i class="fas fa-leaf text-4xl text-green-600 opacity-30"></i>
                        </div>
                        @endif
                        <div class="absolute top-2 right-2 bg-yellow-400 rounded-full p-1">
                            <i class="fas fa-star text-white text-sm"></i>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <h3 class="text-lg font-bold text-gray-800 transition group-hover:text-green-600">{{ $plant->name }}</h3>
                        <p class="mb-3 text-sm text-gray-600">{{ $plant->species }}</p>

                        <div class="mb-3 flex flex-wrap gap-2">
                            @if($plant->is_neglected)
                            <span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-bold text-red-700">Neglected</span>
                            @else
                            <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-bold text-green-700">Healthy</span>
                            @endif
                            <span class="inline-block rounded bg-gray-100 px-2 py-1 text-xs font-bold text-gray-700">{{ $plant->care_consistency }}%</span>
                        </div>

                        <div class="mt-auto border-t border-gray-200 pt-3">
                            <p class="text-xs text-gray-500">Tap to view details</p>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <div class="mb-5 sm:mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">My Plants</h2>
            </div>

            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex flex-col sm:flex-row w-full gap-3">
                    <input id="plant-search" type="text" placeholder="Search plants..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600" />
                    <select id="plant-category-filter" class="w-full sm:w-40 rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="all">All categories</option>
                        <option value="normal">Normal</option>
                        <option value="vegetable">Vegetable</option>
                        <option value="fruit">Fruit</option>
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    <a href="{{ route('plants.create') }}" class="block w-full text-center sm:inline-block shrink-0 rounded-lg bg-green-600 px-3 py-2 text-sm text-white transition hover:bg-green-700 sm:px-4 sm:text-base">
                        <i class="fas fa-plus mr-2"></i>Add Plant
                    </a>
                </div>
            </div>
        </div>

        @if(count($plants) > 0)
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($plants as $plant)
            <a href="{{ route('plants.show', $plant->id) }}" class="group plant-card" data-name="{{ strtolower($plant->name) }}" data-category="{{ $plant->category }}">
                <div class="flex h-full cursor-pointer flex-col overflow-hidden rounded-lg border-2 border-transparent bg-gradient-to-br from-green-50 to-blue-50 transition-all duration-200 hover:border-green-400 hover:shadow-lg">
                    <div class="relative h-40 overflow-hidden bg-gray-200">
                        @if($plant->photo_url)
                        <img src="{{ asset('storage/' . $plant->photo_url) }}" alt="{{ $plant->name }}" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105">
                        @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-green-200 to-blue-200">
                            <i class="fas fa-leaf text-4xl text-green-600 opacity-30"></i>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-1 flex-col p-4">
                        <h3 class="text-lg font-bold text-gray-800 transition group-hover:text-green-600">{{ $plant->name }}</h3>
                        <p class="mb-3 text-sm text-gray-600">{{ $plant->species }}</p>

                        <div class="mb-3 flex flex-wrap gap-2">
                            @if($plant->is_neglected)
                            <span class="inline-block rounded bg-red-100 px-2 py-1 text-xs font-bold text-red-700">Neglected</span>
                            @else
                            <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-bold text-green-700">Healthy</span>
                            @endif
                            <span class="inline-block rounded bg-gray-100 px-2 py-1 text-xs font-bold text-gray-700">{{ $plant->care_consistency }}%</span>
                            <span class="inline-block rounded px-2 py-1 text-xs font-bold mr-2 {{ $plant->category == 'vegetable' ? 'bg-green-100 text-green-700' : ($plant->category == 'fruit' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($plant->category) }}</span>
                        </div>

                        <div class="mt-auto border-t border-gray-200 pt-3">
                            <p class="text-xs text-gray-500">Tap to view details</p>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="py-12 text-center">
            <i class="fas fa-seedling mb-4 text-6xl text-gray-300"></i>
            <p class="mb-4 text-lg text-gray-500">No plants yet. Start your garden!</p>
            <a href="{{ route('plants.create') }}" class="inline-block rounded-lg bg-green-600 px-6 py-3 text-white transition hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Add Your First Plant
            </a>
        </div>
        @endif
    </section>
</div>
@endsection
