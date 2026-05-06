@extends('admin.layouts.app')

@section('page_title', 'Edit Room')
@section('page_subtitle', 'Update room information')

@section('content')
<div class="max-w-2xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.rooms.index') }}" class="text-gray-400 hover:text-yellow-500 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Edit Room</h1>
            <p class="text-gray-400 text-sm mt-1">Update room #{{ $room->room_number }} information</p>
        </div>
    </div>
    
    <form method="POST" action="{{ route('admin.rooms.update', $room->id) }}" class="card rounded-xl p-6" id="roomForm">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="form-label">Room Number <span class="text-red-500">*</span></label>
            <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" 
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
                    <option value="" disabled class="text-gray-400">-- Select Room Type --</option>
                    @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ $room->room_type_id == $type->id ? 'selected' : '' }} class="text-white bg-gray-800">
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
                    <option value="" disabled class="text-gray-400">Pilih Lantai</option>
                    @for($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ $room->floor == $i ? 'selected' : '' }} class="text-white">
                        Lantai {{ $i }} 
                        @if($i == 1) - Lobby & Restaurant
                        @elseif($i >= 2 && $i <= 3) - Deluxe Rooms
                        @elseif($i >= 4 && $i <= 5) - Executive Rooms
                        @elseif($i == 6) - Suite Rooms
                        @elseif($i == 7) - Presidential Suite
                        @elseif($i == 8) - Rooftop & Sky Lounge
                        @endif
                    </option>
                    @endfor
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
                    <option value="available" {{ $room->status == 'available' ? 'selected' : '' }} class="text-green-400">✓ Available - Tersedia</option>
                    <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }} class="text-blue-400">⚠ Occupied - Terisi</option>
                    <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }} class="text-red-400">🔧 Maintenance - Perbaikan</option>
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
        
        <!-- Info Box: Photo from Room Type -->
        <div class="bg-yellow-500/10 rounded-lg p-4 mb-6 border border-yellow-500/20">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-yellow-500 text-xl mt-0.5"></i>
                <div>
                    <p class="text-yellow-500 font-semibold text-sm">Informasi Foto</p>
                    <p class="text-gray-400 text-xs">Foto kamar akan menggunakan foto dari tipe kamar yang dipilih.</p>
                    @if($room->roomType && $room->roomType->photo_url)
                    <div class="mt-2">
                        <p class="text-gray-500 text-xs mb-1">Preview Foto Tipe Kamar:</p>
                        <img src="{{ $room->roomType->photo_url }}" alt="{{ $room->roomType->name }}" class="h-20 w-auto rounded-lg object-cover border border-yellow-500/30">
                        <p class="text-gray-500 text-xs mt-1">Tipe Kamar: <span class="text-yellow-500">{{ $room->roomType->name }}</span></p>
                    </div>
                    @else
                    <p class="text-gray-500 text-xs mt-2">Belum ada foto untuk tipe kamar ini. Anda dapat menambahkannya di menu Room Types.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-8 pt-4 border-t border-gray-700">
            <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition flex items-center gap-2" id="submitBtn">
                <i class="fas fa-save"></i>
                Update Room
            </button>
            <a href="{{ route('admin.rooms.index') }}" class="bg-gray-700 text-gray-300 px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition flex items-center gap-2">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
    /* Dark mode styles for all selects */
    select {
        background-color: #1f2937 !important;
        color: #ffffff !important;
    }
    
    select option {
        background-color: #1f2937 !important;
        color: #ffffff !important;
        padding: 10px !important;
    }
    
    select option:checked {
        background-color: #D4AF37 !important;
        color: #000000 !important;
    }
    
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
</style>
@endsection

@push('scripts')
<script>
    $('#roomForm').on('submit', function() {
        const btn = $('#submitBtn');
        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Updating...');
        btn.prop('disabled', true);
    });
</script>
@endpush