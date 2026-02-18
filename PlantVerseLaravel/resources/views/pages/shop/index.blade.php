@extends('layouts.app')

@section('page-title', 'Shop')

@section('main-content')
<div class="space-y-6">
    <!-- Eligibility Alert -->
    @if(!$isEligible)
    <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Minimum Balance Required:</strong> You need at least 100 PVT to redeem rewards. Current balance: {{ $user->pvt_balance }} PVT
    </div>
    @endif

    <!-- Rewards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rewards as $reward)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <!-- Icon/Image -->
            <div class="h-40 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                <span class="text-6xl">{{ $reward->icon ?? '🎁' }}</span>
            </div>

            <!-- Content -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $reward->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ $reward->description }}</p>

                <!-- Cost -->
                <div class="mb-4 p-3 bg-green-50 rounded-lg text-center">
                    <p class="text-xs text-gray-600 mb-1">Cost:</p>
                    <p class="text-2xl font-bold text-green-600">
                        <i class="fas fa-coins text-yellow-500"></i> {{ $reward->pvt_cost }} PVT
                    </p>
                </div>

                <!-- Redeem Button -->
                @if($user->pvt_balance >= $reward->pvt_cost)
                <form action="{{ route('shop.redeem', $reward->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-shopping-cart mr-2"></i>Redeem
                    </button>
                </form>
                @else
                <button disabled class="w-full bg-gray-400 text-white px-4 py-2 rounded-lg font-medium cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>Not Enough PVT
                </button>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-gift text-6xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 text-lg">No rewards available</p>
        </div>
        @endforelse
    </div>
</div>
@endsection