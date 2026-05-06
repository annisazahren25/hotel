@extends('admin.layouts.app')

@section('page_title', 'Add Room Type')
@section('page_subtitle', 'Create a new room category')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.room-types.index') }}" class="text-gray-400 hover:text-yellow-500 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Add Room Type</h1>
            <p class="text-gray-400 text-sm mt-1">Create a new room category for your hotel</p>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.room-types.store') }}" enctype="multipart/form-data" class="card rounded-xl p-6">
        @csrf
        
        <div class="mb-4">
            <label class="form-label">Room Type Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="form-input @error('name') border-red-500 @enderror" 
                   required placeholder="Contoh: Deluxe Suite, Executive Room">
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Price per Night <span class="text-red-500">*</span></label>
            <input type="number" name="price" value="{{ old('price') }}" 
                   class="form-input @error('price') border-red-500 @enderror" 
                   required placeholder="Contoh: 1500000">
            @error('price')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Capacity (Guests) <span class="text-red-500">*</span></label>
            <input type="number" name="capacity" value="{{ old('capacity', 2) }}" 
                   class="form-input @error('capacity') border-red-500 @enderror" 
                   required min="1" max="10">
            @error('capacity')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Maksimal 10 orang per kamar</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" 
                      class="form-input @error('description') border-red-500 @enderror" 
                      placeholder="Describe the room type...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Photo</label>
            <input type="file" name="photo" accept="image/*" class="form-input" style="padding: 8px;">
            <p class="text-gray-500 text-xs mt-1">Max 10MB. Supported: JPG, PNG, GIF, WEBP</p>
            @error('photo')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-700">
            <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition">
                <i class="fas fa-save mr-2"></i> Save Room Type
            </button>
            <a href="{{ route('admin.room-types.index') }}" class="bg-gray-700 text-gray-300 px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection