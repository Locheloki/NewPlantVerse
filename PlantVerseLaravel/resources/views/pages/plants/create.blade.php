@extends('layouts.app')

@section('page-title', 'Add Plant')

@section('main-content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('plants.index') }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>Back to Plants
    </a>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add a New Plant</h2>

        <form action="{{ route('plants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Plant Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Plant Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., My Monstera" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent" required>
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Species -->
            <div>
                <label for="species" class="block text-sm font-medium text-gray-700 mb-2">Species *</label>
                <input type="text" id="species" name="species" value="{{ old('species') }}" placeholder="e.g., Monstera deliciosa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent" required>
                @error('species')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Upload -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Plant Photo</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-600 transition" onclick="document.getElementById('photo').click()">
                    <div id="photo-preview" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto mb-4 rounded">
                    </div>
                    <div id="photo-placeholder">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2 block"></i>
                        <p class="text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                    </div>
                </div>
                <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
                @error('photo')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Care Recommendations -->
            <div>
                <label for="care_recommendations" class="block text-sm font-medium text-gray-700 mb-2">Care Recommendations</label>
                <textarea id="care_recommendations" name="care_recommendations" placeholder="Any specific care tips for this plant..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent" rows="4"></textarea>
                @error('care_recommendations')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Or Identify with AI -->
            <div class="border-t pt-6">
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Upload a photo and we can identify your plant automatically!
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-check mr-2"></i>Add Plant
                </button>
                <a href="{{ route('plants.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('preview-img').src = event.target.result;
                document.getElementById('photo-placeholder').classList.add('hidden');
                document.getElementById('photo-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection