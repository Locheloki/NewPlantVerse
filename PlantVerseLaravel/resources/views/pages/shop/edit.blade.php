@extends('layouts.app')

@section('page-title', 'Edit Reward')

@section('main-content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6 border border-gray-200">
    <div class="mb-6 pb-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Editing: {{ $reward->title }}</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-gray-700">Cancel</a>
    </div>

    <form action="{{ route('shop.update', $reward->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-[220px_1fr] md:items-start">
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                    @if($reward->image_path)
                    <img src="{{ asset('storage/' . $reward->image_path) }}" alt="{{ $reward->title }}" class="h-48 w-full object-cover">
                    @else
                    <div class="flex h-48 w-full items-center justify-center bg-gradient-to-br from-green-100 to-purple-100">
                        <span class="text-6xl">{{ $reward->icon ?? '🎁' }}</span>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">
                    <p class="mt-2 text-xs text-gray-500">Upload a JPG, PNG, or WebP image up to 4 MB. This appears at the top of the shop listing.</p>
                    @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $reward->title) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>
                @error('title')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="7" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>{{ old('description', $reward->description) }}</textarea>
                <p class="mt-2 text-xs text-gray-500">Use this for product details, benefits, materials, sizes, care notes, or delivery notes.</p>
                @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost (PVT)</label>
                    <input type="number" name="pvt_cost" value="{{ old('pvt_cost', $reward->pvt_cost) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>
                    @error('pvt_cost')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', $reward->icon) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border">
                    @error('icon')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
