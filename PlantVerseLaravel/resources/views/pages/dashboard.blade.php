@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('main-content')
<div class="space-y-5 sm:space-y-8">
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <div class="rounded-lg border-t-4 border-yellow-500 bg-white p-4 shadow sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">PVT Balance</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600 sm:text-3xl">{{ $user->pvt_balance }}</p>
                </div>
                <i class="fas fa-coins text-3xl text-yellow-300 opacity-20 sm:text-4xl"></i>
            </div>
        </div>

        <div class="rounded-lg border-t-4 border-green-500 bg-white p-4 shadow sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 sm:text-sm">Total Plants</p>
                    <p class="mt-2 text-2xl font-bold text-green-600 sm:text-3xl">{{ count($plants) }}</p>
                </div>
                <i class="fas fa-leaf text-3xl text-green-300 opacity-20 sm:text-4xl"></i>
            </div>
        </div>
    </div>

    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <div class="mb-5 flex items-center justify-between gap-3 sm:mb-6">
            <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">My Plants</h2>
            <a href="{{ route('plants.create') }}" class="shrink-0 rounded-lg bg-green-600 px-3 py-2 text-sm text-white transition hover:bg-green-700 sm:px-4 sm:text-base">
                <i class="fas fa-plus mr-2"></i>Add Plant
            </a>
        </div>

        @if(count($plants) > 0)
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($plants as $plant)
            <a href="{{ route('plants.show', $plant->id) }}" class="group">
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

    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Upcoming Care Tasks</h3>
        <div class="space-y-3">
            @forelse($upcomingTasks as $item)
            <div class="flex flex-col gap-3 rounded-lg border border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between {{ $item['isOverdue'] ? 'bg-red-50' : '' }}">
                <div class="flex flex-1 items-center">
                    <span class="mr-3 text-2xl">
                        @switch($item['task']->type)
                        @case('Water')
                        <i class="fas fa-droplet text-blue-500"></i>
                        @break
                        @case('Sunlight')
                        <i class="fas fa-sun text-yellow-500"></i>
                        @break
                        @case('Fertilize')
                        <i class="fas fa-seedling text-green-600"></i>
                        @break
                        @endswitch
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">{{ $item['plant']->name }}</p>
                        <p class="text-sm text-gray-600">{{ $item['task']->type }}</p>
                    </div>
                </div>

                <div class="sm:mr-4 sm:text-right">
                    @if($item['isOverdue'])
                    <span class="font-semibold text-red-600">OVERDUE</span>
                    @elseif($item['daysUntilDue'] > 0)
                    <span class="text-sm text-gray-600">{{ (int) ceil($item['daysUntilDue']) }} days</span>
                    @else
                    <span class="text-sm text-green-600">Due today</span>
                    @endif
                </div>

                <form action="{{ route('plants.log-care', [$item['plant']->id, $item['task']->type]) }}" method="POST" class="sm:inline">
                    @csrf
                    @if($item['isOverdue'] || $item['daysUntilDue'] <= 0)
                    <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 sm:w-auto">
                        <i class="fas fa-check mr-1"></i>Done
                    </button>
                    @else
                    <button type="button" disabled class="w-full cursor-not-allowed rounded-lg bg-gray-400 px-4 py-2 text-white sm:w-auto" title="Available in {{ (int) ceil($item['daysUntilDue']) }} day(s)">
                        <i class="fas fa-clock mr-1"></i>Wait
                    </button>
                    @endif
                </form>
            </div>
            @empty
            <p class="text-gray-500">No upcoming tasks</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
