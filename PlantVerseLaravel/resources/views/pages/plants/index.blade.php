@extends('layouts.app')

@section('page-title', 'My Plants')

@section('main-content')
<div class="space-y-6">
    <!-- Add Button -->
    <div class="flex">
        <a href="{{ route('plants.create') }}" class="flex w-full items-center justify-center rounded-lg bg-green-600 px-4 py-3 text-white hover:bg-green-700 sm:ml-auto sm:w-auto sm:px-6">
            <i class="fas fa-plus mr-2"></i>Add Plant
        </a>
    </div>

    @if(!empty($activeFilter) || !empty($activeCategory))
    <div class="mt-4 flex flex-col gap-2 rounded-lg border border-yellow-100 bg-yellow-50 p-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-yellow-800">
            Showing
            @if(!empty($activeCategory))
            <strong>{{ $activeCategory === 'normal' ? 'Normal Plant' : ucfirst($activeCategory) }}</strong>
            @endif
            @if(!empty($activeCategory) && !empty($activeFilter))
            plants
            @endif
            @if(!empty($activeFilter))
            needing <strong>{{ ucfirst($activeFilter) }}</strong>
            @endif
        </div>
        <div>
            <a href="{{ route('plants.index') }}" class="text-sm text-yellow-700 underline">Show all plants</a>
        </div>
    </div>
    @endif

    <div class="mt-4 space-y-3 rounded-lg border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto] lg:items-end">
            <form method="GET" action="{{ route('plants.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(10rem,14rem)_minmax(10rem,14rem)]">
                <div>
                    <label for="care-filter" class="mb-1 block text-sm font-medium text-gray-600">Care filter</label>
                    <select id="care-filter" name="care_filter" onchange="this.form.submit()" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="">All plants</option>
                        <option value="water" {{ ($activeFilter === 'water') ? 'selected' : '' }}>Need watering</option>
                        <option value="sunlight" {{ ($activeFilter === 'sunlight') ? 'selected' : '' }}>Need sunlight</option>
                        <option value="fertilize" {{ ($activeFilter === 'fertilize') ? 'selected' : '' }}>Need fertilizer</option>
                    </select>
                </div>

                <div>
                    <label for="category-filter" class="mb-1 block text-sm font-medium text-gray-600">Category</label>
                    <select id="category-filter" name="category" onchange="this.form.submit()" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="">All categories</option>
                        <option value="normal" {{ ($activeCategory === 'normal') ? 'selected' : '' }}>Normal Plant</option>
                        <option value="vegetable" {{ ($activeCategory === 'vegetable') ? 'selected' : '' }}>Vegetable</option>
                        <option value="fruit" {{ ($activeCategory === 'fruit') ? 'selected' : '' }}>Fruit</option>
                    </select>
                </div>
            </form>

            <div class="relative w-full lg:max-w-xs">
                <label for="plant-search" class="mb-1 block text-sm font-medium text-gray-600">Search</label>
                <i class="fas fa-search pointer-events-none absolute left-3 top-[2.35rem] text-sm text-gray-400"></i>
                <input id="plant-search" type="search" placeholder="Search name or species..." class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-100">
            </div>
        </div>

        @if($plants->isNotEmpty())
        <form id="bulk-delete-form" method="POST" action="{{ route('plants.bulk-destroy') }}" class="grid grid-cols-1 gap-3 border-t border-gray-100 pt-3 sm:grid-cols-[auto_auto_1fr] sm:items-center lg:flex lg:flex-wrap">
            @csrf
            @method('DELETE')
            <button type="button" id="delete-mode-toggle" class="inline-flex w-full items-center justify-center rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 sm:w-auto" aria-pressed="false">
                <i class="fas fa-trash-alt mr-2"></i>Select to delete
            </button>
            <label id="select-all-wrapper" class="hidden items-center justify-center gap-2 text-sm text-gray-600 sm:justify-start">
                <input type="checkbox" id="select-all-plants" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                Select all
            </label>
            <span id="selected-plants-count" class="hidden text-center text-sm text-gray-500 sm:text-left">0 selected</span>
            <button type="submit" id="bulk-delete-button" class="hidden w-full items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white opacity-50 transition hover:bg-red-700 disabled:cursor-not-allowed sm:w-auto lg:ml-auto" disabled>
                <i class="fas fa-trash mr-2"></i>Delete selected
            </button>
        </form>
        @endif
    </div>

    <!-- Plants Grid -->
    <div id="plants-grid" class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @forelse($plants as $plant)
        <a href="{{ route('plants.show', $plant->id) }}" class="plant-card group block" data-plant-name="{{ strtolower($plant->name) }}" data-plant-species="{{ strtolower($plant->species) }}">
            <div class="relative bg-white rounded-lg shadow hover:shadow-md transition-shadow overflow-hidden {{ $plant->is_favorite ? 'ring-2 ring-yellow-400' : '' }}">
                <label class="bulk-select-label pointer-events-none absolute left-2 top-2 z-10 hidden h-8 w-8 items-center justify-center rounded-full bg-white opacity-0 shadow transition hover:shadow-md" title="Select plant">
                    <input type="checkbox" name="plant_ids[]" value="{{ $plant->id }}" form="bulk-delete-form" class="plant-select h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="sr-only">Select {{ $plant->name }}</span>
                </label>

                <!-- Plant Image -->
                <div class="h-32 bg-gradient-to-br from-green-100 to-green-50 flex items-center justify-center overflow-hidden relative">
                    @if($plant->photo_url)
                    <img src="{{ asset('storage/' . $plant->photo_url) }}" alt="{{ $plant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                    <i class="fas fa-leaf text-4xl text-green-300"></i>
                    @endif

                    <!-- Favorite Button -->
                    <button type="button" class="favorite-btn absolute top-2 right-2 bg-white rounded-full p-1.5 shadow hover:shadow-lg transition-all z-10" data-plant-id="{{ $plant->id }}" data-is-favorite="{{ $plant->is_favorite ? 'true' : 'false' }}">
                        <i class="fas fa-star text-lg {{ $plant->is_favorite ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                    </button>
                </div>

                <!-- Plant Info -->
                <div class="p-3">
                    <h3 class="text-sm font-bold text-gray-800 truncate">{{ $plant->name }}</h3>
                    <p class="text-xs text-gray-600 truncate">{{ $plant->species }}</p>

                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="care-consistency-bar bg-green-500 h-1.5 rounded-full" data-care-consistency="{{ $plant->care_consistency }}"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $plant->care_consistency }}%</p>
                        </div>
                    </div>

                    @if($plant->is_neglected)
                    <div class="mt-2 text-center">
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded inline-block">Neglected</span>
                    </div>
                    @endif
                </div>
            </div>
        </a>
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

    <div id="plants-empty-search" class="hidden py-12 text-center">
        <i class="fas fa-search mb-4 block text-5xl text-gray-300"></i>
        <p class="text-gray-500">No plants match your search.</p>
    </div>

    @push('scripts')
    <script>
        function setupFavoriteButtons() {
            const buttons = document.querySelectorAll('.favorite-btn');
            console.log('Found ' + buttons.length + ' favorite buttons');

            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const plantId = this.dataset.plantId;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const token = csrfToken ? csrfToken.getAttribute('content') : '';

                    console.log('Toggle favorite clicked for plant ID:', plantId);

                    fetch('/my-plants/' + plantId + '/toggle-favorite', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        })
                        .then(function(response) {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            console.log('Success:', data);
                            const icon = btn.querySelector('i');
                            const card = btn.closest('.bg-white');

                            if (data.is_favorite) {
                                icon.classList.remove('text-gray-300');
                                icon.classList.add('text-yellow-400');
                                btn.dataset.isFavorite = 'true';
                                if (card) {
                                    card.classList.add('ring-2', 'ring-yellow-400');
                                }
                            } else {
                                icon.classList.remove('text-yellow-400');
                                icon.classList.add('text-gray-300');
                                btn.dataset.isFavorite = 'false';
                                if (card) {
                                    card.classList.remove('ring-2', 'ring-yellow-400');
                                }
                            }

                            // Show toast
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                            toast.textContent = data.message;
                            document.body.appendChild(toast);
                            setTimeout(function() {
                                toast.remove();
                            }, 3000);
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                            alert('Error: Failed to toggle favorite');
                        });
                });
            });
        }

        function setupCareConsistencyBars() {
            document.querySelectorAll('.care-consistency-bar').forEach(function(bar) {
                const value = Number.parseInt(bar.dataset.careConsistency || '0', 10);
                const percentage = Math.min(100, Math.max(0, Number.isNaN(value) ? 0 : value));
                bar.style.width = percentage + '%';
            });
        }

        function setupBulkDelete() {
            const form = document.getElementById('bulk-delete-form');
            const modeToggle = document.getElementById('delete-mode-toggle');
            const selectAllWrapper = document.getElementById('select-all-wrapper');
            const selectAll = document.getElementById('select-all-plants');
            const checkboxes = Array.from(document.querySelectorAll('.plant-select'));
            const countLabel = document.getElementById('selected-plants-count');
            const deleteButton = document.getElementById('bulk-delete-button');
            let deleteModeActive = false;

            if (!form || !modeToggle || !selectAllWrapper || !selectAll || !countLabel || !deleteButton || checkboxes.length === 0) {
                return;
            }

            function visibleCheckboxes() {
                return checkboxes.filter(function(checkbox) {
                    const card = checkbox.closest('.plant-card');
                    return !card || !card.classList.contains('hidden');
                });
            }

            function updateBulkDeleteState() {
                const selectedCount = checkboxes.filter(function(checkbox) {
                    return checkbox.checked;
                }).length;
                const visible = visibleCheckboxes();
                const visibleSelectedCount = visible.filter(function(checkbox) {
                    return checkbox.checked;
                }).length;

                countLabel.textContent = selectedCount + (selectedCount === 1 ? ' selected' : ' selected');
                deleteButton.disabled = selectedCount === 0;
                deleteButton.classList.toggle('opacity-50', selectedCount === 0);
                selectAll.checked = visible.length > 0 && visibleSelectedCount === visible.length;
                selectAll.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visible.length;
                modeToggle.setAttribute('aria-pressed', deleteModeActive ? 'true' : 'false');
                modeToggle.classList.toggle('bg-red-600', deleteModeActive);
                modeToggle.classList.toggle('text-white', deleteModeActive);
                modeToggle.classList.toggle('border-red-600', deleteModeActive);
                modeToggle.classList.toggle('bg-white', !deleteModeActive);
                modeToggle.classList.toggle('text-red-600', !deleteModeActive);
                modeToggle.classList.toggle('border-red-200', !deleteModeActive);
                modeToggle.innerHTML = deleteModeActive ?
                    '<i class="fas fa-times mr-2"></i>Cancel delete' :
                    '<i class="fas fa-trash-alt mr-2"></i>Select to delete';

                selectAllWrapper.classList.toggle('hidden', !deleteModeActive);
                selectAllWrapper.classList.toggle('inline-flex', deleteModeActive);
                countLabel.classList.toggle('hidden', !deleteModeActive);
                deleteButton.classList.toggle('hidden', !deleteModeActive);
                deleteButton.classList.toggle('inline-flex', deleteModeActive);

                document.querySelectorAll('.bulk-select-label').forEach(function(label) {
                    label.classList.toggle('hidden', !deleteModeActive);
                    label.classList.toggle('flex', deleteModeActive);
                    label.classList.toggle('pointer-events-none', !deleteModeActive);
                    label.classList.toggle('opacity-0', !deleteModeActive);
                    label.classList.toggle('opacity-100', deleteModeActive);
                });
            }

            modeToggle.addEventListener('click', function() {
                deleteModeActive = !deleteModeActive;

                if (!deleteModeActive) {
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });
                }

                updateBulkDeleteState();
            });

            selectAll.addEventListener('change', function() {
                visibleCheckboxes().forEach(function(checkbox) {
                    checkbox.checked = selectAll.checked;
                });
                updateBulkDeleteState();
            });

            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
                checkbox.addEventListener('change', updateBulkDeleteState);
            });

            document.querySelectorAll('.bulk-select-label').forEach(function(label) {
                label.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            });

            document.querySelectorAll('.plant-card').forEach(function(card) {
                card.addEventListener('click', function(event) {
                    if (!deleteModeActive) {
                        return;
                    }

                    const clickedControl = event.target.closest('button, input, label');
                    if (clickedControl && !clickedControl.classList.contains('bulk-select-label')) {
                        return;
                    }

                    event.preventDefault();
                    const checkbox = card.querySelector('.plant-select');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        updateBulkDeleteState();
                    }
                });
            });

            form.addEventListener('submit', function(event) {
                const selectedCount = checkboxes.filter(function(checkbox) {
                    return checkbox.checked;
                }).length;

                if (selectedCount === 0) {
                    event.preventDefault();
                    return;
                }

                const plantText = selectedCount === 1 ? 'plant' : 'plants';
                if (!confirm('Delete ' + selectedCount + ' selected ' + plantText + '? This cannot be undone.')) {
                    event.preventDefault();
                }
            });

            updateBulkDeleteState();
            window.updatePlantBulkDeleteState = updateBulkDeleteState;
        }

        function setupPlantSearch() {
            const searchInput = document.getElementById('plant-search');
            const cards = Array.from(document.querySelectorAll('.plant-card'));
            const emptySearch = document.getElementById('plants-empty-search');

            if (!searchInput || cards.length === 0 || !emptySearch) {
                return;
            }

            function filterPlants() {
                const query = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach(function(card) {
                    const name = card.dataset.plantName || '';
                    const species = card.dataset.plantSpecies || '';
                    const isVisible = query === '' || name.includes(query) || species.includes(query);

                    card.classList.toggle('hidden', !isVisible);
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                emptySearch.classList.toggle('hidden', visibleCount > 0);

                if (typeof window.updatePlantBulkDeleteState === 'function') {
                    window.updatePlantBulkDeleteState();
                }
            }

            searchInput.addEventListener('input', filterPlants);
            filterPlants();
        }

        // Setup when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupFavoriteButtons();
                setupCareConsistencyBars();
                setupBulkDelete();
                setupPlantSearch();
            });
        } else {
            setupFavoriteButtons();
            setupCareConsistencyBars();
            setupBulkDelete();
            setupPlantSearch();
        }
    </script>
    @endpush
</div>
@endsection
