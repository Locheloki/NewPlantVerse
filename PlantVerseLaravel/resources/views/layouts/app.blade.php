@extends('app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <div class="hidden md:flex md:flex-col md:w-64 md:bg-white md:shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-green-600">🌿 PlantVerse</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-chart-line mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('plants.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('plants.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-leaf mr-3"></i>
                <span>My Plants</span>
            </a>
            <a href="{{ route('milestones.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('milestones.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-trophy mr-3"></i>
                <span>Milestones</span>
            </a>
            <a href="{{ route('shop.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('shop.*') ? 'bg-green-50 text-green-600' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-store mr-3"></i>
                <span>Shop</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-200">
            <div class="flex flex-col bg-green-50 p-3 rounded-lg">
                <div class="mb-2">
                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-red-600 hover:text-red-800 font-medium flex items-center mt-2 pt-2 border-t border-green-200 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <button class="md:hidden mr-4 text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'PlantVerse')</h2>

                @if(auth()->check() && auth()->user()->isAdmin())
                <span class="ml-4 px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded border border-purple-300 shadow-sm flex items-center">
                    <i class="fas fa-user-shield mr-1"></i> Administrator Mode
                </span>
                @endif
            </div>

            <div class="flex items-center space-x-4">
                @auth
                <div class="flex items-center bg-green-100 px-3 py-2 rounded-lg">
                    <i class="fas fa-coins text-yellow-500 mr-2"></i>
                    <span class="font-semibold text-green-700">{{ auth()->user()->pvt_balance ?? 0 }} PVT</span>
                </div>
                @endauth
            </div>
        </div>

        <div class="flex-1 overflow-auto">
            <div class="p-6">
                @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>{{ $message }}
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                </div>
                @endif

                @yield('main-content')
            </div>
        </div>
    </div>
</div>
@endsection