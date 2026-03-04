@extends('layouts.app')

@section('page-title', 'Edit Reward')

@section('main-content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6 border border-gray-200">
    <div class="mb-6 pb-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Editing: {{ $reward->title }}</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-gray-700">Cancel</a>
    </div>

    <form action="{{ route('shop.update', $reward->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $reward->title) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>{{ old('description', $reward->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost (PVT)</label>
                    <input type="number" name="pvt_cost" value="{{ old('pvt_cost', $reward->pvt_cost) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', $reward->icon) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 p-2 border">
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