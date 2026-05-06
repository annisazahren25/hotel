@extends('admin.layouts.app')

@section('page_title', 'Edit Room Type')
@section('page_subtitle', 'Update room category information')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.room-types.index') }}" class="text-gray-400 hover:text-yellow-500 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Room Type</h1>
            <p class="text-gray-400 text-sm mt-1">Update {{ $roomType->name }} information</p>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.room-types.update', $roomType->id) }}" enctype="multipart/form-data" class="card rounded-xl p-6" id="roomTypeForm">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="form-label">Room Type Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $roomType->name) }}" 
                   class="form-input @error('name') border-red-500 @enderror" 
                   required placeholder="Contoh: Deluxe Suite, Executive Room">
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Price per Night <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">Rp</span>
                <input type="number" name="price" value="{{ old('price', $roomType->price) }}" 
                       class="form-input @error('price') border-red-500 @enderror pl-10" 
                       required placeholder="1500000">
            </div>
            @error('price')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Harga per malam dalam Rupiah</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Capacity (Guests) <span class="text-red-500">*</span></label>
            <select name="capacity" class="form-input @error('capacity') border-red-500 @enderror" required>
                <option value="1" {{ $roomType->capacity == 1 ? 'selected' : '' }}>1 Guest (Single)</option>
                <option value="2" {{ $roomType->capacity == 2 ? 'selected' : '' }}>2 Guests (Double/Twin)</option>
                <option value="3" {{ $roomType->capacity == 3 ? 'selected' : '' }}>3 Guests (Triple)</option>
                <option value="4" {{ $roomType->capacity == 4 ? 'selected' : '' }}>4 Guests (Family)</option>
                <option value="5" {{ $roomType->capacity == 5 ? 'selected' : '' }}>5 Guests (Suite)</option>
                <option value="6" {{ $roomType->capacity == 6 ? 'selected' : '' }}>6 Guests (Executive)</option>
                <option value="7" {{ $roomType->capacity == 7 ? 'selected' : '' }}>7 Guests (Presidential)</option>
                <option value="8" {{ $roomType->capacity == 8 ? 'selected' : '' }}>8 Guests (Royal Suite)</option>
                <option value="9" {{ $roomType->capacity == 9 ? 'selected' : '' }}>9 Guests (Grand Suite)</option>
                <option value="10" {{ $roomType->capacity == 10 ? 'selected' : '' }}>10 Guests (Mega Suite)</option>
            </select>
            @error('capacity')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Jumlah maksimal tamu yang bisa menginap</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" 
                      class="form-input @error('description') border-red-500 @enderror" 
                      placeholder="Describe the room type...">{{ old('description', $roomType->description) }}</textarea>
            @error('description')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Deskripsi lengkap tentang tipe kamar ini</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Current Photo</label>
            @if($roomType->photo_url)
            <div class="mt-2 mb-3">
                <img src="{{ $roomType->photo_url }}" alt="{{ $roomType->name }}" class="h-32 w-auto rounded-lg object-cover border border-yellow-500/30">
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remove_photo" value="1" class="mr-2">
                        <span class="text-sm text-red-400">Remove current photo</span>
                    </label>
                </div>
            </div>
            @else
            <div class="mt-2 mb-3 p-4 bg-white/5 rounded-lg text-center border border-dashed border-gray-600">
                <i class="fas fa-image text-3xl text-gray-500 mb-2"></i>
                <p class="text-gray-500 text-sm">No photo uploaded yet</p>
            </div>
            @endif
        </div>
        
        <div class="mb-4">
            <label class="form-label">Change Photo (Optional)</label>
            <input type="file" name="photo" accept="image/*" class="form-input" style="padding: 8px;" id="photoInput">
            <p class="text-gray-500 text-xs mt-1">Max 10MB. Supported: JPG, PNG, GIF, WEBP. Kosongkan jika tidak ingin mengubah foto.</p>
            <div id="photoPreview" class="mt-3 hidden">
                <p class="text-gray-400 text-sm mb-2">New Photo Preview:</p>
                <img id="previewImage" class="h-32 rounded-lg object-cover border border-yellow-500/30">
            </div>
            @error('photo')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Room Type Statistics (Read-only) -->
        <div class="mb-4 p-4 bg-white/5 rounded-lg">
            <p class="text-sm text-gray-400 mb-2">Room Statistics</p>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $roomType->rooms()->count() }}</p>
                    <p class="text-xs text-gray-500">Total Rooms</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-400">{{ $roomType->rooms()->where('status', 'available')->count() }}</p>
                    <p class="text-xs text-gray-500">Available</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-400">{{ $roomType->rooms()->where('status', 'occupied')->count() }}</p>
                    <p class="text-xs text-gray-500">Occupied</p>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-700">
            <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition flex items-center gap-2" id="submitBtn">
                <i class="fas fa-save"></i>
                Update Room Type
            </button>
            <a href="{{ route('admin.room-types.index') }}" class="bg-gray-700 text-gray-300 px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition flex items-center gap-2">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Photo preview
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photoPreview');
                const previewImage = document.getElementById('previewImage');
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('photoPreview').classList.add('hidden');
        }
    });
    
    // Form submit loading state
    document.getElementById('roomTypeForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
        btn.disabled = true;
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection