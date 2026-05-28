@extends('layouts.app')

@section('page-title', 'Shop')

@section('main-content')
<div class="space-y-6">
    @if(!$isEligible)
    <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Minimum Balance Required:</strong> You need at least <span class="font-bold">{{ $rewards->min('pvt_cost') }} PVT</span> to redeem rewards. Current balance: <span class="font-bold">{{ $user->pvt_balance }} PVT</span>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($rewards as $reward)
        <div class="overflow-hidden rounded-lg bg-white shadow-lg transition-shadow hover:shadow-xl">
            <div class="relative h-56 bg-gray-100">
                @if($reward->image_path)
                <img src="{{ asset('storage/' . $reward->image_path) }}" alt="{{ $reward->title }}" class="h-full w-full object-cover">
                @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-green-100 to-purple-100">
                    @if($reward->icon)
                    <span class="text-6xl">{{ $reward->icon }}</span>
                    @else
                    <i class="fas fa-gift text-6xl text-purple-400"></i>
                    @endif
                </div>
                @endif

                @if(in_array($reward->id, $ownedRewardIds))
                <div class="absolute right-3 top-3 flex items-center gap-1 rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white">
                    <i class="fas fa-check-circle"></i> Owned
                </div>
                @endif
            </div>

            <div class="p-6">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-xl font-bold text-gray-900">{{ $reward->title }}</h3>
                    </div>

                    @if($reward->icon)
                    <span class="shrink-0 text-3xl">{{ $reward->icon }}</span>
                    @endif
                </div>

                <p class="mb-4 whitespace-pre-line text-sm leading-6 text-gray-600">{{ $reward->description }}</p>

                <div class="mb-4 rounded-lg bg-green-50 p-3 text-center">
                    <p class="mb-1 text-xs text-gray-600">Cost:</p>
                    <p class="text-2xl font-bold text-green-600">
                        <i class="fas fa-coins text-yellow-500"></i> {{ $reward->pvt_cost }} PVT
                    </p>
                </div>

                @if(in_array($reward->id, $ownedRewardIds))
                <a href="{{ route('checkout.show', $reward->id) }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-center font-medium text-white transition hover:bg-blue-700">
                    <i class="fas fa-truck"></i>Checkout & Deliver
                </a>
                @elseif($user->pvt_balance >= $reward->pvt_cost)
                <form action="{{ route('shop.redeem', $reward->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700">
                        <i class="fas fa-shopping-cart mr-2"></i>Redeem
                    </button>
                </form>
                @else
                <button disabled class="w-full cursor-not-allowed rounded-lg bg-gray-400 px-4 py-2 font-medium text-white">
                    <i class="fas fa-lock mr-2"></i>Not Enough PVT
                </button>
                @endif

                @if(auth()->user()->isAdmin())
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <a href="{{ route('shop.edit', $reward->id) }}" class="block w-full rounded-lg bg-purple-100 px-4 py-2 text-center font-medium text-purple-700 transition hover:bg-purple-200">
                        <i class="fas fa-edit mr-2"></i>Edit Listing
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center">
            <i class="fas fa-gift mb-4 block text-6xl text-gray-300"></i>
            <p class="text-lg text-gray-500">No rewards available</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
