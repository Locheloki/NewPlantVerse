@extends('layouts.app')

@section('page-title', 'My Orders')

@section('main-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
            <p class="text-gray-600 mt-1">Track your reward deliveries</p>
        </div>
        <a href="{{ route('shop.index') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
            <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
        </a>
    </div>

    <!-- Status Summary Cards -->
    @if(array_sum($orderStats) > 0)
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-600">{{ $orderStats['pending'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Pending</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-blue-500">
            <p class="text-2xl font-bold text-blue-600">{{ $orderStats['processing'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Processing</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-purple-600">{{ $orderStats['shipped'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Shipped</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-green-500">
            <p class="text-2xl font-bold text-green-600">{{ $orderStats['delivered'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Delivered</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-600">{{ $orderStats['cancelled'] }}</p>
            <p class="text-xs text-gray-600 mt-1">Cancelled</p>
        </div>
    </div>
    @endif

    <!-- Orders List -->
    @forelse($orders as $order)
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
        <div class="p-6 flex items-center justify-between">
            <div class="flex items-start gap-4 flex-1">
                <!-- Reward Icon -->
                <div class="text-4xl">{{ $order->reward->icon ?? '🎁' }}</div>

                <!-- Order Info -->
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-800">{{ $order->reward->title }}</h3>

                    <!-- Status Badge -->
                    <div class="mt-2 flex items-center gap-2">
                        @switch($order->status)
                        @case('pending')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-clock"></i> Pending
                        </span>
                        <p class="text-xs text-gray-600">Waiting for processing</p>
                        @break
                        @case('processing')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-cog"></i> Processing
                        </span>
                        <p class="text-xs text-gray-600">Preparing your order</p>
                        @break
                        @case('shipped')
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-truck"></i> Shipped
                        </span>
                        @if($order->tracking_number)
                        <p class="text-xs text-gray-600">Tracking: {{ $order->tracking_number }}</p>
                        @endif
                        @break
                        @case('delivered')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-check-circle"></i> Delivered
                        </span>
                        <p class="text-xs text-gray-600">Successfully delivered</p>
                        @break
                        @case('cancelled')
                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> Cancelled
                        </span>
                        @break
                        @endswitch
                    </div>

                    <!-- Delivery Address -->
                    <p class="text-xs text-gray-600 mt-3">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Delivering to: {{ $order->city }}, {{ $order->state_province }}
                    </p>

                    <!-- Order Date -->
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>
                        Ordered: {{ $order->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-2 ml-4">
                <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition whitespace-nowrap">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
                @if(!$order->isShipped())
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="inline" onclick="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm rounded-lg font-medium transition whitespace-nowrap">
                        <i class="fas fa-trash mr-1"></i>Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
        <h3 class="text-xl font-bold text-gray-700 mb-2">No Orders Yet</h3>
        <p class="text-gray-600 mb-6">You haven't placed any reward orders yet.</p>
        <a href="{{ route('shop.index') }}" class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
            <i class="fas fa-shopping-bag mr-2"></i>Go to Shop
        </a>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection