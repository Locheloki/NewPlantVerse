@extends('layouts.app')

@section('page-title', 'Checkout - ' . $reward->title)

@section('main-content')
<div class="max-w-2xl mx-auto">
    <!-- Progress Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Shop
        </a>
    </div>

    <!-- Checkout Container -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Checkout Form (Left) -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Delivery Address</h1>
                <p class="text-gray-600 mb-6">Enter your address so we can deliver your reward</p>

                <form action="{{ route('checkout.store', $reward->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $defaultData['full_name']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('full_name') border-red-500 @enderror"
                            required>
                        @error('full_name')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $defaultData['email']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                            required>
                        @error('email')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $defaultData['phone']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                            placeholder="+1 (555) 000-0000"
                            required>
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Street Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                        <input type="text" name="street_address" value="{{ old('street_address', $defaultData['street_address']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('street_address') border-red-500 @enderror"
                            placeholder="123 Main Street, Apt 4B"
                            required>
                        @error('street_address')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $defaultData['city']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @enderror"
                            placeholder="New York"
                            required>
                        @error('city')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State/Province and Postal Code -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                            <input type="text" name="state_province" value="{{ old('state_province', $defaultData['state_province']) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('state_province') border-red-500 @enderror"
                                placeholder="NY"
                                required>
                            @error('state_province')
                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $defaultData['postal_code']) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror"
                                placeholder="10001"
                                required>
                            @error('postal_code')
                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" value="{{ old('country', $defaultData['country']) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-500 @enderror"
                            placeholder="United States"
                            required>
                        @error('country')
                        <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 pt-4">
                        <a href="{{ route('shop.index') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-check mr-2"></i>Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Summary (Right) -->
        <div>
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Order Summary</h2>

                <!-- Reward Info -->
                <div class="text-center mb-6">
                    <div class="inline-block text-5xl mb-3">{{ $reward->icon ?? '🎁' }}</div>
                    <h3 class="font-bold text-gray-800">{{ $reward->title }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ $reward->description }}</p>
                </div>

                <!-- Status -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-600 font-medium mb-1">ORDER STATUS</p>
                    <p class="text-sm font-semibold text-blue-900">Ready to Ship</p>
                </div>

                <!-- Delivery Info -->
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">DELIVERY DETAILS</p>
                        <p class="text-sm text-gray-700 mt-1">
                            <i class="fas fa-shipping-fast text-green-600 mr-2"></i>
                            Standard Shipping
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 font-medium mb-1">ESTIMATED ARRIVAL</p>
                        <p class="text-sm text-gray-700">3-7 business days</p>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Your reward will be processed and shipped to the address provided. You'll receive a tracking number via email.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection