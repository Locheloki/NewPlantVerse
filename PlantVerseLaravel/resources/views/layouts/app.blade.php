@extends('app')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <aside class="hidden md:flex md:w-64 md:flex-col md:bg-white md:shadow-lg">
        <div class="border-b border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-green-600">PlantVerse</h1>
        </div>

        <nav class="flex-1 space-y-2 px-4 py-6">
            <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('plants.index') }}" class="flex items-center rounded-lg px-4 py-3 {{ request()->routeIs('plants.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-leaf mr-3"></i>
                <span>My Plants</span>
            </a>
            <a href="{{ route('milestones.index') }}" class="flex items-center rounded-lg px-4 py-3 {{ request()->routeIs('milestones.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-trophy mr-3"></i>
                <span>Milestones</span>
            </a>
            <a href="{{ route('shop.index') }}" class="flex items-center rounded-lg px-4 py-3 {{ request()->routeIs('shop.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-store mr-3"></i>
                <span>Shop</span>
            </a>

            @if(auth()->check() && auth()->user()->isAdmin())
            <hr class="my-2 border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-4 py-3 {{ request()->routeIs('admin.*') ? 'bg-purple-50 text-purple-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-user-shield mr-3"></i>
                <span class="font-semibold">Admin Panel</span>
            </a>
            @endif
        </nav>

        <div class="border-t border-gray-200 p-4">
            <div class="flex flex-col rounded-lg bg-green-50 p-3">
                <div class="mb-2">
                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="mt-2 flex w-full items-center border-t border-green-200 pt-2 text-left text-sm font-medium text-red-600 transition-colors hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
        <header class="sticky top-0 z-20 bg-white px-4 py-3 shadow-sm sm:px-6 sm:py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-semibold text-gray-800 sm:text-xl">@yield('page-title', 'PlantVerse')</h2>

                    @if(auth()->check() && auth()->user()->isAdmin())
                    <span class="mt-1 inline-flex items-center rounded border border-purple-300 bg-purple-100 px-2 py-1 text-xs font-bold text-purple-700 shadow-sm">
                        <i class="fas fa-user-shield mr-1"></i> Administrator Mode
                    </span>
                    @endif
                </div>

                <div class="shrink-0">
                    @auth
                    <div class="flex items-center rounded-lg bg-green-100 px-2 py-2 sm:px-3">
                        <i class="fas fa-coins mr-2 text-yellow-500"></i>
                        <span class="text-sm font-semibold text-green-700 sm:text-base">{{ auth()->user()->pvt_balance ?? 0 }} PVT</span>
                    </div>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-auto">
            <div class="px-3 py-4 pb-24 sm:p-6 md:pb-6">
                @if ($message = Session::get('success'))
                <div class="mb-4 rounded-lg border border-green-400 bg-green-100 p-4 text-green-700">
                    <i class="fas fa-check-circle mr-2"></i>{{ $message }}
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="mb-4 rounded-lg border border-red-400 bg-red-100 p-4 text-red-700">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                </div>
                @endif

                @yield('main-content')
            </div>
        </main>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 z-30 grid grid-cols-5 border-t border-gray-200 bg-white shadow-[0_-4px_12px_rgba(0,0,0,0.06)] md:hidden">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-2 py-2 text-xs {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-gray-600' }}">
            <i class="fas fa-chart-line text-base"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('plants.index') }}" class="flex flex-col items-center gap-1 px-2 py-2 text-xs {{ request()->routeIs('plants.*') ? 'text-green-600' : 'text-gray-600' }}">
            <i class="fas fa-leaf text-base"></i>
            <span>Plants</span>
        </a>
        <a href="{{ route('milestones.index') }}" class="flex flex-col items-center gap-1 px-2 py-2 text-xs {{ request()->routeIs('milestones.*') ? 'text-green-600' : 'text-gray-600' }}">
            <i class="fas fa-trophy text-base"></i>
            <span>Goals</span>
        </a>
        <a href="{{ route('shop.index') }}" class="flex flex-col items-center gap-1 px-2 py-2 text-xs {{ request()->routeIs('shop.*') ? 'text-green-600' : 'text-gray-600' }}">
            <i class="fas fa-store text-base"></i>
            <span>Shop</span>
        </a>
        @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 px-2 py-2 text-xs {{ request()->routeIs('admin.*') ? 'text-purple-600' : 'text-gray-600' }}">
            <i class="fas fa-user-shield text-base"></i>
            <span>Admin</span>
        </a>
        @else
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full flex-col items-center gap-1 px-2 py-2 text-xs text-gray-600">
                <i class="fas fa-sign-out-alt text-base"></i>
                <span>Logout</span>
            </button>
        </form>
        @endif
    </nav>
</div>
@endsection
