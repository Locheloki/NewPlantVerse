@extends('layouts.app')

@section('page-title', 'Customize Care Schedule - ' . $plant->name)

@section('main-content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('plants.show', $plant) }}" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Plant
        </a>
        <h1 class="text-3xl font-bold text-gray-800">📋 Customize Care Schedule</h1>
        <p class="text-gray-600 mt-2">{{ $plant->name }} ({{ $plant->species }})</p>
    </div>

    <!-- Research Reminder Card -->
    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-8">
        <div class="flex items-start gap-4">
            <div class="text-3xl">🔍</div>
            <div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">📚 Research Your Plant First!</h3>
                <p class="text-blue-800 mb-3">
                    Before customizing, we recommend researching your specific plant variety to understand its optimal care requirements. Different plants have very different needs!
                </p>
                <p class="text-sm text-blue-700 mb-3">
                    <strong>📌 Tips for researching:</strong>
                </p>
                <ul class="text-sm text-blue-700 space-y-1 ml-4">
                    <li>✓ Check the plant's care tag or label it came with</li>
                    <li>✓ Search for "[Plant Species] care guide" online</li>
                    <li>✓ Check gardening sites like Gardening Know How or The Spruce</li>
                    <li>✓ Consider your local climate and light conditions</li>
                    <li>✓ Visit r/houseplants or plant care forums</li>
                </ul>
                <p class="text-blue-700 mt-3">
                    💡 <strong>Common ranges:</strong> Watering (1-14 days), Sunlight (daily), Fertilizing (7-365 days depending on season)
                </p>
            </div>
        </div>
    </div>

    <!-- Current Care Schedule -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Current Care Schedule</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            @foreach($careTasks as $task)
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
                <div class="text-3xl mb-2">
                    @if($task->type === 'Water')
                    💧
                    @elseif($task->type === 'Sunlight')
                    ☀️
                    @elseif($task->type === 'Fertilize')
                    🌿
                    @else
                    🌱
                    @endif
                </div>
                <p class="text-gray-600 font-medium">{{ $task->type }}</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $task->frequency_days }} <span class="text-lg">day{{ $task->frequency_days > 1 ? 's' : '' }}</span></p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Customization Form -->
    <form action="{{ route('plants.care-schedule.update', $plant) }}" method="POST" class="bg-white rounded-lg shadow-lg p-8">
        @csrf
        @method('PUT')

        <h2 class="text-xl font-bold text-gray-800 mb-6">🎯 Customize Frequencies</h2>

        <div class="space-y-6 mb-8">
            <!-- Water Frequency -->
            <div class="border-l-4 border-blue-500 pl-4 py-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    💧 Water Schedule (days between watering)
                </label>
                <p class="text-xs text-gray-600 mb-3">
                    💡 Typical range: 1-14 days. Desert plants: 14+ days. Tropical plants: 2-7 days
                </p>
                <div class="flex items-center gap-4">
                    <input
                        type="number"
                        name="water_frequency"
                        value="{{ $careTasks->firstWhere('type', 'Water')?->frequency_days ?? 7 }}"
                        min="1"
                        max="365"
                        class="w-24 px-4 py-2 border border-gray-300 rounded-lg text-center font-semibold"
                        required />
                    <span class="text-gray-600">days</span>
                </div>
                @error('water_frequency')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sunlight Frequency -->
            <div class="border-l-4 border-yellow-500 pl-4 py-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    ☀️ Sunlight Check (days between checks)
                </label>
                <p class="text-xs text-gray-600 mb-3">
                    💡 Recommended: 1 day (daily monitoring). Low-light plants: 3-7 days
                </p>
                <div class="flex items-center gap-4">
                    <input
                        type="number"
                        name="sunlight_frequency"
                        value="{{ $careTasks->firstWhere('type', 'Sunlight')?->frequency_days ?? 1 }}"
                        min="1"
                        max="365"
                        class="w-24 px-4 py-2 border border-gray-300 rounded-lg text-center font-semibold"
                        required />
                    <span class="text-gray-600">days</span>
                </div>
                @error('sunlight_frequency')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fertilize Frequency -->
            <div class="border-l-4 border-green-500 pl-4 py-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    🌿 Fertilize Schedule (days between feedings)
                </label>
                <p class="text-xs text-gray-600 mb-3">
                    💡 Typical range: 7-365 days. Growing season: 14-30 days. Dormant season: 60-365 days
                </p>
                <div class="flex items-center gap-4">
                    <input
                        type="number"
                        name="fertilize_frequency"
                        value="{{ $careTasks->firstWhere('type', 'Fertilize')?->frequency_days ?? 30 }}"
                        min="1"
                        max="365"
                        class="w-24 px-4 py-2 border border-gray-300 rounded-lg text-center font-semibold"
                        required />
                    <span class="text-gray-600">days</span>
                </div>
                @error('fertilize_frequency')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Important Note -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <p class="text-sm text-yellow-800">
                <strong>⚠️ Important:</strong> These settings will take effect immediately. Your existing task schedules won't reset, but new frequencies will apply going forward.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <button
                type="submit"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-bold transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Save Custom Schedule
            </button>
            <a
                href="{{ route('plants.show', $plant) }}"
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-bold transition text-center">
                Cancel
            </a>
        </div>
    </form>

    <!-- Help Tip -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <p class="text-sm text-indigo-800">
            <strong>💡 Tip:</strong> You can always come back and adjust these frequencies if your plant isn't thriving. Plant care is part art, part science!
        </p>
    </div>

</div>
@endsection