@extends('admin.layouts.app')

@section('page_title', 'Add New Room')
@section('page_subtitle', 'Create a new hotel room')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="text-gray-400 hover:text-yellow-500 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Add New Room</h1>
            <p class="text-gray-400 text-sm mt-1">Create a new room for your hotel</p>
        </div>
    </div>
    
    @if($errors->any())
    <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
            <div>
                <p class="text-red-400 font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-red-400 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
    
    <form method="POST" action="{{ route('admin.rooms.store') }}" class="card rounded-xl p-6" id="roomForm">
        @csrf
        
        <div class="mb-4">
            <label class="form-label">Room Number <span class="text-red-500">*</span></label>
            <input type="text" name="room_number" value="{{ old('room_number') }}" 
                   class="form-input @error('room_number') border-red-500 @enderror" 
                   required placeholder="Contoh: 101, 201, 301">
            @error('room_number')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Format: Nomor unik untuk setiap kamar</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Room Type <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="room_type_id" class="form-input @error('room_type_id') border-red-500 @enderror" required>
                    <option value="" disabled {{ old('room_type_id') ? '' : 'selected' }} class="text-gray-400">-- Select Room Type --</option>
                    @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }} class="text-white bg-gray-800">
                        {{ $type->name }} - Rp {{ number_format($type->price, 0, ',', '.') }} / night
                    </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            @error('room_type_id')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Foto kamar akan otomatis menggunakan foto dari tipe kamar yang dipilih</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Floor (Lantai) <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="floor" class="form-input @error('floor') border-red-500 @enderror" required>
                    <option value="" disabled {{ old('floor') ? '' : 'selected' }} class="text-gray-400">-- Pilih Lantai --</option>
                    <option value="1" {{ old('floor') == 1 ? 'selected' : '' }} class="text-white">Lantai 1 - Lobby & Restaurant</option>
                    <option value="2" {{ old('floor') == 2 ? 'selected' : '' }} class="text-white">Lantai 2 - Deluxe Rooms</option>
                    <option value="3" {{ old('floor') == 3 ? 'selected' : '' }} class="text-white">Lantai 3 - Deluxe Rooms</option>
                    <option value="4" {{ old('floor') == 4 ? 'selected' : '' }} class="text-white">Lantai 4 - Executive Rooms</option>
                    <option value="5" {{ old('floor') == 5 ? 'selected' : '' }} class="text-white">Lantai 5 - Executive Rooms</option>
                    <option value="6" {{ old('floor') == 6 ? 'selected' : '' }} class="text-white">Lantai 6 - Suite Rooms</option>
                    <option value="7" {{ old('floor') == 7 ? 'selected' : '' }} class="text-white">Lantai 7 - Presidential Suite</option>
                    <option value="8" {{ old('floor') == 8 ? 'selected' : '' }} class="text-white">Lantai 8 - Rooftop & Sky Lounge</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            @error('floor')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-500 text-xs mt-1">Lantai 1-8 sesuai dengan gedung hotel</p>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Status <span class="text-red-500">*</span></label>
            <div class="relative">
                <select name="status" class="form-input @error('status') border-red-500 @enderror" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }} class="text-green-400">✓ Available - Tersedia</option>
                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }} class="text-blue-400">⚠ Occupied - Terisi</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }} class="text-red-400">🔧 Maintenance - Perbaikan</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            @error('status')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="bg-yellow-500/10 rounded-lg p-4 mb-6 border border-yellow-500/20">
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle text-yellow-500 text-xl"></i>
                <div>
                    <p class="text-yellow-500 font-semibold text-sm">Informasi Foto</p>
                    <p class="text-gray-400 text-xs">Foto kamar akan menggunakan foto dari tipe kamar yang dipilih. Anda dapat mengatur foto tipe kamar di menu Room Types.</p>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-700">
            <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition" id="submitBtn">
                <i class="fas fa-save"></i> Save Room
            </button>
            <a href="{{ route('admin.rooms.index') }}" class="bg-gray-700 text-gray-300 px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<style>
    .form-input {
        width: 100%;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 8px;
        padding: 10px 16px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #D4AF37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }
    
    .form-label {
        display: block;
        color: #9ca3af;
        margin-bottom: 8px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    select option {
        background-color: #1f2937;
        color: white;
    }
</style>

<script>
    // Simple form submit with loading state - WITHOUT blocking the form
    document.getElementById('roomForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            btn.disabled = true;
        }
    });
</script>
@endsection