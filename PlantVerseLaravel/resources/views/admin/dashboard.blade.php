@extends('layouts.app')

@section('page-title', 'Admin')

@section('main-content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 px-3 py-4 sm:p-6">
    <div class="mx-auto max-w-7xl space-y-5 sm:space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-green-700 sm:text-4xl">Admin Control Panel</h1>
            <p class="mt-1 text-sm text-gray-600 sm:text-base">Manage users and run diagnostic commands</p>
        </div>

        <section class="rounded-lg border-l-4 border-purple-500 bg-white p-4 shadow-md sm:p-6">
            <h2 class="mb-4 text-xl font-bold text-purple-700 sm:text-2xl">Global Commands</h2>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
                <button onclick="runCommand('neglect')"
                    class="rounded-lg bg-red-500 px-4 py-3 font-bold text-white shadow-md transition hover:bg-red-600">
                    Run Plant Neglect Check
                </button>

                <button onclick="runCommand('attendance')"
                    class="rounded-lg bg-blue-500 px-4 py-3 font-bold text-white shadow-md transition hover:bg-blue-600">
                    Run Attendance Streak Update
                </button>
            </div>

            <div id="commandOutput" class="mt-6 hidden max-h-96 overflow-y-auto rounded bg-gray-900 p-4 font-mono text-sm text-green-400">
                <div id="commandText"></div>
            </div>
        </section>

        <section class="rounded-lg border-l-4 border-blue-500 bg-white p-4 shadow-md sm:p-6">
            <h2 class="mb-6 text-xl font-bold text-blue-700 sm:text-2xl">User Management</h2>

            @forelse ($users as $user)
            <article class="mb-6 border-b pb-6 last:mb-0 last:border-b-0">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <h3 class="break-words text-lg font-bold text-gray-800 sm:text-xl">{{ $user->name }}</h3>
                        <p class="break-all text-sm text-gray-500 sm:text-base">{{ $user->email }}</p>
                        @if ($user->is_admin)
                        <span class="mt-2 inline-block rounded bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800">ADMIN</span>
                        @endif
                    </div>

                    <div class="sm:text-right">
                        <p class="text-xl font-bold text-yellow-600 sm:text-2xl">{{ $user->pvt_balance }} PVT</p>
                        <p class="text-sm text-gray-500">{{ $user->plants->count() }} plants</p>
                    </div>
                </div>

                <div class="mb-4 rounded-lg border-l-4 border-blue-500 bg-gradient-to-r from-blue-50 to-indigo-50 p-4">
                    <p class="text-sm text-gray-600">Vacation</p>
                    <p class="text-lg font-semibold">
                        @if ($user->is_on_vacation)
                        <span class="text-blue-600">Active</span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <button data-action="toggle-vacation" data-user-id="{{ $user->id }}"
                        class="rounded bg-blue-500 px-3 py-3 text-sm font-bold text-white transition hover:bg-blue-600 sm:py-2">
                        {{ $user->is_on_vacation ? 'End Vacation' : 'Start Vacation' }}
                    </button>

                    <div class="flex gap-2">
                        <input type="number" id="pvt-input-{{ $user->id }}" placeholder="PVT" min="1" max="10000"
                            class="min-w-0 flex-1 rounded border border-gray-300 px-2 py-3 text-sm sm:w-24 sm:flex-none sm:py-2">
                        <button data-action="add-pvt" data-user-id="{{ $user->id }}"
                            class="rounded bg-yellow-500 px-4 py-3 text-sm font-bold text-white transition hover:bg-yellow-600 sm:py-2">
                            Add
                        </button>
                    </div>
                </div>

                <div id="status-{{ $user->id }}" class="mt-2 text-sm font-semibold text-gray-600"></div>

                @if ($user->plants->count() > 0)
                <div class="mt-4 rounded bg-gray-50 p-3">
                    <p class="mb-2 text-sm font-semibold text-gray-700">Plants:</p>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        @foreach ($user->plants as $plant)
                        <div class="rounded border border-gray-200 bg-white p-3 text-sm">
                            <div>
                                <span class="font-semibold">{{ $plant->name }}</span>
                                <span class="text-gray-500">({{ $plant->species }})</span>
                                @if ($plant->is_neglected)
                                <span class="ml-2 font-bold text-red-600">⚠️ Neglected</span>
                                @else
                                <span class="ml-2 font-bold text-green-600">✓ Healthy</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </article>
            @empty
            <p class="py-8 text-center text-gray-500">No users found</p>
            @endforelse
        </section>
    </div>
</div>

<script>
    // Event delegation for data-action buttons
    document.addEventListener('click', function(e) {
        const button = e.target.closest('button[data-action]');
        if (!button) return;

        const action = button.dataset.action;
        const userId = button.dataset.userId;

        if (action === 'toggle-vacation') {
            toggleVacation(userId);
        } else if (action === 'add-pvt') {
            addPVT(userId);
        }
    });

    function toggleVacation(userId) {
        fetch(`/admin/test/toggle-vacation/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                showStatus(userId, data.message);
                location.reload();
            })
            .catch(e => showStatus(userId, 'Error: ' + e.message));
    }

    function addPVT(userId) {
        const amount = document.getElementById(`pvt-input-${userId}`).value;
        if (!amount) {
            alert('Enter PVT amount');
            return;
        }

        fetch(`/admin/test/add-pvt/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: parseInt(amount)
                })
            })
            .then(r => r.json())
            .then(data => {
                showStatus(userId, data.message);
                location.reload();
            })
            .catch(e => showStatus(userId, 'Error: ' + e.message));
    }

    function showStatus(userId, message) {
        const el = document.getElementById(`status-${userId}`);
        el.textContent = message;
        el.classList.add('text-blue-600', 'font-bold');
        setTimeout(() => el.textContent = '', 3000);
    }

    function runCommand(type) {
        const endpoints = {
            neglect: '/admin/command/plant-neglect',
            attendance: '/admin/command/attendance-streak',
        };
        const endpoint = endpoints[type];

        if (!endpoint) return;

        const output = document.getElementById('commandOutput');
        const text = document.getElementById('commandText');

        output.classList.remove('hidden');
        text.textContent = 'Running...';

        fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                text.textContent = data.output || data.message;
            })
            .catch(e => text.textContent = 'Error: ' + e.message);
    }
</script>
@endsection