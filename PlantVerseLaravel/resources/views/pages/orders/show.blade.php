@extends('layouts.app')

@section('page-title', 'Order #' . $order->id)

@section('main-content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>

    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Order #{{ $order->id }}</h1>
                <p class="text-gray-600 text-sm mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t H:i A') }}</p>
            </div>

            <!-- Status Badge -->
            @switch($order->status)
            @case('pending')
            <div class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-clock text-lg"></i>
                <div>
                    <p class="font-bold text-sm">Pending</p>
                    <p class="text-xs">Waiting for processing</p>
                </div>
            </div>
            @break
            @case('processing')
            <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-cog text-lg animate-spin"></i>
                <div>
                    <p class="font-bold text-sm">Processing</p>
                    <p class="text-xs">Preparing your order</p>
                </div>
            </div>
            @break
            @case('shipped')
            <div class="px-4 py-2 bg-purple-100 text-purple-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-truck text-lg"></i>
                <div>
                    <p class="font-bold text-sm">Shipped</p>
                    <p class="text-xs">On its way to you</p>
                </div>
            </div>
            @break
            @case('delivered')
            <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-lg"></i>
                <div>
                    <p class="font-bold text-sm">Delivered</p>
                    <p class="text-xs">Successfully received</p>
                </div>
            </div>
            @break
            @case('cancelled')
            <div class="px-4 py-2 bg-red-100 text-red-800 rounded-lg flex items-center gap-2">
                <i class="fas fa-times-circle text-lg"></i>
                <div>
                    <p class="font-bold text-sm">Cancelled</p>
                    <p class="text-xs">Order cancelled</p>
                </div>
            </div>
            @break
            @endswitch
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Order Details (Left) -->
        <div class="md:col-span-2 space-y-6">
            <!-- Reward Details -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Reward Details</h2>
                <div class="flex items-start gap-4">
                    <div class="text-6xl">{{ $order->reward->icon ?? '🎁' }}</div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800">{{ $order->reward->title }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $order->reward->description }}</p>
                        <div class="mt-4 p-3 bg-green-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Cost:</p>
                            <p class="text-lg font-bold text-green-600">
                                <i class="fas fa-coins text-yellow-500"></i> {{ $order->reward->pvt_cost }} PVT
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Delivery Address
                </h2>
                <div class="space-y-2">
                    <p class="font-semibold text-gray-800">{{ $order->full_name }}</p>
                    <p class="text-gray-700">{{ $order->street_address }}</p>
                    <p class="text-gray-700">{{ $order->city }}, {{ $order->state_province }} {{ $order->postal_code }}</p>
                    <p class="text-gray-700">{{ $order->country }}</p>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope text-gray-500 mr-2"></i>{{ $order->email }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-phone text-gray-500 mr-2"></i>{{ $order->phone }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tracking Information -->
            @if($order->isShipped())
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-box text-purple-600 mr-2"></i>Tracking Information
                </h2>
                @if($order->tracking_number)
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Tracking Number</p>
                        <p class="text-lg font-mono font-bold text-gray-800 mt-1">{{ $order->tracking_number }}</p>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Use this tracking number to monitor your package with the courier.
                        </p>
                    </div>
                </div>
                @else
                <p class="text-gray-600">Tracking information will be available shortly.</p>
                @endif
            </div>
            @endif

            <!-- Order Timeline -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Order Timeline</h2>
                <div class="space-y-4">
                    <!-- Ordered -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold text-gray-800">Order Placed</p>
                            <p class="text-sm text-gray-600">{{ $order->created_at->format('F d, Y \a\t H:i A') }}</p>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) bg-blue-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center">
                                <i class="fas @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) fa-cog @else fa-ellipsis-h @endif"></i>
                            </div>
                            @if(in_array($order->status, ['shipped', 'delivered']))
                            <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                            @endif
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) text-gray-800 @else text-gray-500 @endif">Processing</p>
                            <p class="text-sm @if(in_array($order->status, ['processing', 'shipped', 'delivered'])) text-gray-600 @else text-gray-400 @endif">Your order is being prepared</p>
                        </div>
                    </div>

                    <!-- Shipped -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 @if(in_array($order->status, ['shipped', 'delivered'])) bg-purple-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center">
                                <i class="fas @if(in_array($order->status, ['shipped', 'delivered'])) fa-truck @else fa-ellipsis-h @endif"></i>
                            </div>
                            @if($order->status === 'delivered')
                            <div class="w-1 h-12 bg-gray-300 mt-2"></div>
                            @endif
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold @if(in_array($order->status, ['shipped', 'delivered'])) text-gray-800 @else text-gray-500 @endif">Shipped</p>
                            <p class="text-sm @if(in_array($order->status, ['shipped', 'delivered'])) text-gray-600 @else text-gray-400 @endif">Your reward is on its way</p>
                        </div>
                    </div>

                    <!-- Delivered -->
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 @if($order->status === 'delivered') bg-green-500 @else bg-gray-300 @endif text-white rounded-full flex items-center justify-center">
                                <i class="fas @if($order->status === 'delivered') fa-check-circle @else fa-ellipsis-h @endif"></i>
                            </div>
                        </div>
                        <div class="pb-4">
                            <p class="font-semibold @if($order->status === 'delivered') text-gray-800 @else text-gray-500 @endif">Delivered</p>
                            <p class="text-sm @if($order->status === 'delivered') text-gray-600 @else text-gray-400 @endif">Your reward has arrived</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right) -->
        <div>
            <!-- Quick Info -->
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Order Summary</h2>

                <div class="space-y-4">
                    <!-- Order Number -->
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-600 font-medium uppercase">Order Number</p>
                        <p class="text-lg font-bold text-gray-800 mt-1">#{{ $order->id }}</p>
                    </div>

                    <!-- Status -->
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-600 font-medium uppercase">Status</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1 capitalize">{{ $order->status }}</p>
                    </div>

                    <!-- Estimated Delivery -->
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-xs text-gray-600 font-medium uppercase">Est. Delivery</p>
                        <p class="text-sm text-gray-700 mt-1">3-7 business days</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 space-y-2">
                        @if(!$order->isShipped())
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onclick="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition">
                                <i class="fas fa-trash mr-2"></i>Cancel Order
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('shop.index') }}" class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-shopping-bag mr-2"></i>Shop More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection