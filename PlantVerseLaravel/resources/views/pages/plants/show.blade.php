@extends('layouts.app')

@section('page-title', $plant->name)

@section('main-content')
<div class="space-y-6">
    <!-- Header with Back Button and Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('plants.index') }}" class="inline-flex items-center text-green-600 hover:text-green-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Plants
        </a>
        <div class="flex gap-3">
            <a
                href="{{ route('plants.edit', $plant) }}"
                class="text-blue-600 hover:text-blue-700 inline-flex items-center transition"
                title="Edit plant information and photo">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            <button
                onclick="openDeleteModal()"
                class="text-red-600 hover:text-red-700 inline-flex items-center transition"
                title="Delete this plant">
                <i class="fas fa-trash-alt mr-1"></i>Delete
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
        id="deleteModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="if(event.target === this) closeDeleteModal()">
        <div class="bg-white rounded-lg shadow-xl max-w-sm mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-center text-gray-800 mb-2">Delete {{ $plant->name }}?</h3>
                <p class="text-gray-600 text-center mb-6">This action cannot be undone. All care tasks and plant data will be permanently deleted.</p>
                <div class="flex gap-4">
                    <button
                        type="button"
                        onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition">
                        Cancel
                    </button>
                    <form
                        id="deleteForm"
                        action="{{ route('plants.destroy', $plant) }}"
                        method="POST"
                        class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Plant Image and Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Image -->
                <div class="h-64 bg-gradient-to-br from-green-100 to-green-50 flex items-center justify-center overflow-hidden">
                    @if($plant->photo_url)
                    <img src="{{ asset('storage/' . $plant->photo_url) }}" alt="{{ $plant->name }}" class="w-full h-full object-cover">
                    @else
                    <i class="fas fa-leaf text-8xl text-green-300"></i>
                    @endif
                </div>

                <!-- Info -->
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $plant->name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $plant->species }}</p>

                    <!-- Care Consistency -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold text-gray-700">Care Consistency</span>
                            <span class="text-xl font-bold text-green-600">{{ $plant->care_consistency }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="care-consistency-bar bg-green-500 h-3 rounded-full transition-all" data-care-consistency="{{ $plant->care_consistency }}"></div>
                        </div>
                    </div>

                    <!-- Status -->
                    @if($plant->is_neglected)
                    <div class="p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Needs Attention!</strong> This plant has been neglected.
                    </div>
                    @else
                    <div class="p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Healthy!</strong> Keep up the good care!
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Care Schedule and Advice -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Care Schedule -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar-check text-green-600 mr-2"></i>Care Schedule
                    </h3>
                    <a href="{{ route('plants.care-schedule.edit', $plant) }}" class="text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-1 rounded-lg transition">
                        <i class="fas fa-sliders-h mr-1"></i>Customize
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($plant->careTasks as $task)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">
                                    @switch($task->type)
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
                                    <p class="font-semibold text-gray-800">{{ $task->type }}</p>
                                    <p class="text-sm text-gray-600">Every {{ $task->frequency_days }} days</p>
                                </div>
                            </div>
                            <form action="{{ route('plants.log-care', [$plant->id, $task->type]) }}" method="POST" class="inline">
                                @csrf
                                @if($task->isReadyForCompletion())
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-check mr-1"></i>Done
                                </button>
                                @else
                                <button type="button" disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm cursor-not-allowed" title="Come back in {{ $task->daysUntilReady() }} day(s)">
                                    <i class="fas fa-clock mr-1"></i>{{ $task->daysUntilReady() }}d
                                </button>
                                @endif
                            </form>
                        </div>
                        <p class="text-xs text-gray-500">Last completed: {{ $task->last_completed->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-gray-500">No care tasks scheduled</p>
                    @endforelse
                </div>
            </div>

            <!-- Plant Gallery -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-images text-green-600 mr-2"></i>{{ $plant->name }}'s Gallery
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Keep progress photos of this plant as it grows and changes.</p>
                    </div>
                </div>

                <form action="{{ route('plants.journal.store', $plant->id) }}" method="POST" enctype="multipart/form-data" class="mb-6 rounded-lg border border-green-100 bg-green-50 p-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                        <div>
                            <label for="gallery-photo" class="mb-1 block text-sm font-medium text-gray-700">Add gallery photo</label>
                            <input id="gallery-photo" name="photo" type="file" accept="image/*" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm" required>
                            @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 sm:w-auto">
                            <i class="fas fa-plus mr-2"></i>Add Photo
                        </button>
                    </div>
                    <div class="mt-3">
                        <label for="gallery-note" class="mb-1 block text-sm font-medium text-gray-700">Note</label>
                        <textarea id="gallery-note" name="note" rows="2" placeholder="Optional note about what changed..." class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"></textarea>
                        @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </form>

                @php
                $galleryEntries = $plant->journals->whereNotNull('photo_url')->sortByDesc('created_at');
                @endphp

                @if($galleryEntries->isNotEmpty())
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($galleryEntries as $entry)
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                        <img src="{{ asset('storage/' . $entry->photo_url) }}" alt="{{ $plant->name }} gallery photo" class="h-44 w-full object-cover">
                        <div class="p-3">
                            <p class="text-xs text-gray-500">{{ $entry->created_at->format('M d, Y') }}</p>
                            @if($entry->note)
                            <p class="mt-1 text-sm text-gray-700">{{ $entry->note }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center">
                    <i class="fas fa-images mb-3 block text-4xl text-gray-300"></i>
                    <p class="font-medium text-gray-700">No gallery photos yet</p>
                    <p class="mt-1 text-sm text-gray-500">Add photos to document {{ $plant->name }} over time.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        // Alpine components are ready
    });

    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    document.querySelectorAll('.care-consistency-bar').forEach(function(bar) {
        const value = Number.parseInt(bar.dataset.careConsistency || '0', 10);
        const percentage = Math.min(100, Math.max(0, Number.isNaN(value) ? 0 : value));
        bar.style.width = percentage + '%';
    });
</script>
@endsection
