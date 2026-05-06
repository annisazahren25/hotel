@extends('admin.layouts.app')

@section('page_title', 'Room Types Management')
@section('page_subtitle', 'Manage all room types and categories')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Room Types</h1>
            <p class="text-gray-400 text-sm mt-1">Manage room categories, prices, and capacities</p>
        </div>
        <a href="{{ route('admin.room-types.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Add Room Type
        </a>
    </div>

    @if($roomTypes->isEmpty())
    <div class="card rounded-xl p-12 text-center">
        <i class="fas fa-bed text-6xl text-gray-600 mb-4"></i>
        <h3 class="text-xl text-white mb-2">No Room Types Yet</h3>
        <p class="text-gray-400 mb-4">Get started by creating your first room type</p>
        <a href="{{ route('admin.room-types.create') }}" class="bg-yellow-500 text-black px-4 py-2 rounded-lg inline-flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Create Room Type
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roomTypes as $type)
        <div class="card rounded-xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300">
            <!-- Photo -->
            @if($type->photo_url)
            <img src="{{ $type->photo_url }}" alt="{{ $type->name }}" class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                <i class="fas fa-hotel text-5xl text-gray-600"></i>
            </div>
            @endif
            
            <div class="p-5">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-white">{{ $type->name }}</h3>
                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs">
                        {{ $type->rooms_count }} Rooms
                    </span>
                </div>
                
                <div class="mb-3">
                    <span class="text-2xl font-bold text-yellow-500">
                        Rp {{ number_format($type->price, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-400 text-sm">/night</span>
                </div>
                
                <div class="flex items-center gap-2 mb-3 text-sm">
                    <i class="fas fa-users text-gray-400"></i>
                    <span class="text-gray-300">Capacity: {{ $type->capacity }} guests</span>
                </div>
                
                @if($type->description)
                <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ $type->description }}</p>
                @endif
                
                <div class="flex gap-2 pt-3 border-t border-gray-700">
                    <a href="{{ route('admin.room-types.edit', $type->id) }}" 
                       class="flex-1 text-center bg-yellow-500/20 text-yellow-500 px-3 py-2 rounded-lg hover:bg-yellow-500/30 transition">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <button type="button" 
                            onclick="deleteRoomType({{ $type->id }}, '{{ $type->name }}')" 
                            class="flex-1 text-center bg-red-500/20 text-red-500 px-3 py-2 rounded-lg hover:bg-red-500/30 transition">
                        <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function deleteRoomType(id, name) {
        Swal.fire({
            title: 'Delete Room Type?',
            html: `Are you sure you want to delete <strong class="text-yellow-500">${name}</strong>?<br><br>This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/room-types/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            location.reload();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function(xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to delete room type');
                    }
                });
            }
        });
    }
</script>
@endpush