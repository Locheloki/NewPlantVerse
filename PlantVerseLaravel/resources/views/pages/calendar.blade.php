@extends('layouts.app')

@section('page-title', 'Care Calendar')

@section('main-content')
<div class="space-y-5 sm:space-y-8">
    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Care Calendar</h2>
        <p class="text-sm text-gray-600">Showing tasks for the next 14 days. Click a day to view tasks.</p>
    </section>

    <section class="rounded-lg bg-white p-4 shadow sm:p-6">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Calendar - Next 14 Days (Grid)</h3>

        <div class="w-full">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
                @php
                $start = \Carbon\Carbon::parse($calendarStart);
                $end = \Carbon\Carbon::parse($calendarEnd);
                $cursor = $start->copy();
                @endphp

                @while($cursor->lte($end))
                @php
                $key = $cursor->toDateString();
                $items = $calendar[$key] ?? [];
                $hasWater = collect($items)->contains(function($i){ return str_contains(strtolower($i['task']->type), 'water'); });
                $hasSun = collect($items)->contains(function($i){ return str_contains(strtolower($i['task']->type), 'sun'); });
                $hasFert = collect($items)->contains(function($i){ return str_contains(strtolower($i['task']->type), 'fertil'); });
                @endphp

                <button type="button" data-date="{{ $key }}" class="calendar-day flex h-24 sm:h-28 w-full flex-col justify-between rounded-lg border p-3 text-left hover:shadow-sm {{ $cursor->isToday() ? 'ring-2 ring-green-300' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="text-xs text-gray-500">{{ $cursor->format('D') }}</div>
                        @if(count($items) > 0)
                        <div class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">{{ count($items) }}</div>
                        @endif
                    </div>

                    <div class="text-sm font-semibold text-gray-800">{{ $cursor->format('M j') }}</div>

                    <div class="flex items-center gap-2">
                        @if($hasWater)<span class="inline-block h-3 w-3 rounded-full bg-blue-400" title="Water"></span>@endif
                        @if($hasSun)<span class="inline-block h-3 w-3 rounded-full bg-yellow-400" title="Sunlight"></span>@endif
                        @if($hasFert)<span class="inline-block h-3 w-3 rounded-full bg-green-400" title="Fertilize"></span>@endif
                    </div>
                </button>

                @php $cursor->addDay(); @endphp
                @endwhile
            </div>

            <div class="mt-6">
                <h4 id="calendar-selected" class="text-sm text-gray-700 mb-3">Select a day to view tasks</h4>
                <div id="calendar-tasks" class="space-y-3 max-h-96 overflow-auto sm:max-h-full">
                    <p class="text-sm text-gray-500">No date selected.</p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script id="calendar-data" type="application/json">
        @json($calendar)
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendar = JSON.parse(document.getElementById('calendar-data').textContent || '{}');
            const tasksContainer = document.getElementById('calendar-tasks');
            const selectedHeading = document.getElementById('calendar-selected');

            function renderTasksFor(date) {
                tasksContainer.innerHTML = '';
                const d = new Date(date);
                selectedHeading.textContent = d.toDateString();
                const items = calendar[date] || [];
                if (items.length === 0) {
                    tasksContainer.innerHTML = '<p class="text-sm text-gray-500">No tasks for this day.</p>';
                    return;
                }

                items.forEach(it => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between rounded-lg border border-gray-200 p-3';
                    const left = document.createElement('div');
                    left.innerHTML = `<div class="font-medium text-gray-800">${it.plant.name}</div><div class="text-sm text-gray-600">${it.task.type}</div>`;
                    const right = document.createElement('div');
                    const type = (it.task.type || '').toLowerCase();
                    if (type.includes('water')) {
                        right.innerHTML = '<span class="inline-block w-3 h-3 rounded-full bg-blue-400 mr-2" title="Water"></span>';
                    }
                    if (type.includes('sun')) {
                        right.innerHTML += '<span class="inline-block w-3 h-3 rounded-full bg-yellow-400 mr-2" title="Sunlight"></span>';
                    }
                    if (type.includes('fertil')) {
                        right.innerHTML += '<span class="inline-block w-3 h-3 rounded-full bg-green-400 mr-2" title="Fertilize"></span>';
                    }
                    div.appendChild(left);
                    div.appendChild(right);
                    tasksContainer.appendChild(div);
                });
            }

            document.querySelectorAll('.calendar-day').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.calendar-day').forEach(b => b.classList.remove('bg-green-50'));
                    this.classList.add('bg-green-50');
                    renderTasksFor(this.dataset.date);
                    // scroll into view the tasks panel on small screens
                    const tasksPanel = document.getElementById('calendar-tasks');
                    if (window.innerWidth < 768 && tasksPanel) {
                        tasksPanel.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Auto-select today if available
            const todayBtn = document.querySelector('.calendar-day[data-date="' + new Date().toISOString().slice(0, 10) + '"]');
            if (todayBtn) todayBtn.click();
        });
    </script>
    @endpush
</div>
@endsection