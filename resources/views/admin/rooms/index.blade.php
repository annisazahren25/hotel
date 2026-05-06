@extends('admin.layouts.app')

@section('page_title', 'Room Management')
@section('page_subtitle', 'Manage all hotel rooms')

@section('content')
<div class="fade-in">
    <!-- Header with Stats -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Room Management</h1>
            <p class="text-gray-400 text-sm mt-1">Manage and monitor all hotel rooms</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" id="addRoomBtn" class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Add New Room
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Rooms</p>
                    <p class="text-2xl font-bold text-white" id="totalRooms">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bed text-yellow-500"></i>
                </div>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Available</p>
                    <p class="text-2xl font-bold text-green-400" id="availableRooms">{{ $stats['available'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Occupied</p>
                    <p class="text-2xl font-bold text-blue-400" id="occupiedRooms">{{ $stats['occupied'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-blue-500"></i>
                </div>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Maintenance</p>
                    <p class="text-2xl font-bold text-red-400" id="maintenanceRooms">{{ $stats['maintenance'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Floor Filter -->
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <button class="floor-filter px-4 py-2 rounded-lg text-sm transition all-rooms active bg-yellow-500/20 text-yellow-500" data-floor="all">
            All Rooms
        </button>
        @for($i = 1; $i <= 8; $i++)
        <button class="floor-filter px-4 py-2 rounded-lg text-sm transition text-white bg-gray-800 hover:bg-gray-700" data-floor="{{ $i }}">
            Floor {{ $i }}
        </button>
        @endfor
    </div>

    <!-- Rooms Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="table-container">
            <table class="admin-table" id="roomsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Number</th>
                        <th>Photo</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Price / Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="roomsTableBody">
                    @forelse($rooms as $room)
                    <tr data-room-id="{{ $room->id }}" data-floor="{{ $room->floor }}">
                        <td class="text-gray-400">{{ $room->id }}</td>
                        <td class="font-semibold text-white">#{{ $room->room_number }}</td>
                        <td>
                            @if($room->roomType && $room->roomType->photo_url)
                                <img src="{{ $room->roomType->photo_url }}" alt="{{ $room->roomType->name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 bg-gray-700 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-bed text-gray-500 text-lg"></i>
                                </div>
                            @endif
                        </td>
                        <td class="text-gray-300">{{ $room->roomType->name ?? '-' }}</td>
                        <td>
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-500">
                                Floor {{ $room->floor }}
                            </span>
                        </td>
                        <td class="text-white font-semibold">
                            Rp {{ number_format($room->roomType->price ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            <select class="status-select text-xs px-2 py-1 rounded-full font-semibold cursor-pointer transition
                                @if($room->status == 'available') bg-green-500/20 text-green-500 border-green-500/50
                                @elseif($room->status == 'occupied') bg-blue-500/20 text-blue-500 border-blue-500/50
                                @else bg-red-500/20 text-red-500 border-red-500/50 @endif" 
                                data-room-id="{{ $room->id }}">
                                <option value="available" {{ $room->status == 'available' ? 'selected' : '' }} class="bg-gray-800 text-green-400">✓ Available</option>
                                <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }} class="bg-gray-800 text-blue-400">⚠ Occupied</option>
                                <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }} class="bg-gray-800 text-red-400">🔧 Maintenance</option>
                            </select>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.rooms.edit', $room->id) }}" 
                                   class="text-yellow-500 hover:text-yellow-400 transition"
                                   title="Edit Room">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        onclick="deleteRoom({{ $room->id }}, '{{ $room->room_number }}')" 
                                        class="text-red-500 hover:text-red-400 transition"
                                        title="Delete Room">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <button type="button" 
                                        onclick="viewRoomDetails({{ $room->id }})" 
                                        class="text-blue-500 hover:text-blue-400 transition"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-center">
                                <i class="fas fa-bed text-5xl text-gray-600 mb-3"></i>
                                <p class="text-gray-400">No rooms found</p>
                                <a href="{{ route('admin.rooms.create') }}" class="inline-block mt-3 text-yellow-500 hover:text-yellow-400">
                                    + Add your first room
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-container {
        overflow-x: auto;
        border-radius: 12px;
    }
    
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .admin-table th {
        text-align: left;
        padding: 16px;
        background: rgba(255, 255, 255, 0.05);
        color: #9ca3af;
        font-weight: 600;
        font-size: 0.875rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .admin-table td {
        padding: 16px;
        color: #e5e7eb;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .admin-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
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
    
    .status-available {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }
    .status-occupied {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }
    .status-maintenance {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }
    
    /* Floor filter button styles */
    .floor-filter {
        background-color: #1f2937;
        color: #ffffff !important;
        border: 1px solid #374151;
    }
    
    .floor-filter:hover {
        background-color: #374151;
        color: #D4AF37 !important;
    }
    
    .floor-filter.active {
        background-color: rgba(212, 175, 55, 0.2);
        color: #D4AF37 !important;
        border-color: #D4AF37;
    }
</style>
@endsection

@push('scripts')
<script>
    // Floor filter functionality
    $('.floor-filter').on('click', function() {
        $('.floor-filter').removeClass('active bg-yellow-500/20 text-yellow-500');
        $(this).addClass('active bg-yellow-500/20 text-yellow-500');
        
        const floor = $(this).data('floor');
        
        if (floor === 'all') {
            $('#roomsTableBody tr').show();
        } else {
            $('#roomsTableBody tr').hide();
            $(`#roomsTableBody tr[data-floor="${floor}"]`).show();
        }
    });
    
    // Update room status with AJAX
    $('.status-select').on('change', function() {
        const roomId = $(this).data('room-id');
        const newStatus = $(this).val();
        const selectElement = $(this);
        
        let statusText = '';
        switch(newStatus) {
            case 'available': statusText = 'Available'; break;
            case 'occupied': statusText = 'Occupied'; break;
            case 'maintenance': statusText = 'Maintenance'; break;
        }
        
        Swal.fire({
            title: 'Update Room Status?',
            text: `Change room status to ${statusText}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937'
                });
                
                $.ajax({
                    url: `/admin/rooms/${roomId}/status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                confirmButtonColor: '#D4AF37',
                                background: '#1f2937',
                                color: '#fff',
                                timer: 1500
                            });
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update status',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    }
                });
            }
        });
    });
    
    // Delete room function
    window.deleteRoom = function(roomId, roomNumber) {
        Swal.fire({
            title: 'Delete Room?',
            html: `Are you sure you want to delete room <strong class="text-yellow-500">#${roomNumber}</strong>?<br><br>This action cannot be undone!`,
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
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937'
                });
                
                $.ajax({
                    url: `/admin/rooms/${roomId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                confirmButtonColor: '#D4AF37',
                                background: '#1f2937',
                                color: '#fff'
                            });
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to delete room',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    };
    
    // View room details
    window.viewRoomDetails = function(roomId) {
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#1f2937'
        });
        
        $.ajax({
            url: `/admin/rooms/details/${roomId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.close();
                if (response.success && response.room) {
                    const room = response.room;
                    const formattedPrice = new Intl.NumberFormat('id-ID').format(room.price);
                    
                    let statusClass = '';
                    if (room.status === 'available') statusClass = 'status-available';
                    else if (room.status === 'occupied') statusClass = 'status-occupied';
                    else statusClass = 'status-maintenance';
                    
                    let modalHtml = `
                        <div class="text-left space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Room Number:</span>
                                <span class="text-white font-semibold">#${room.room_number}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Floor:</span>
                                <span class="text-white">Floor ${room.floor}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Type:</span>
                                <span class="text-white">${room.type}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Price:</span>
                                <span class="text-yellow-500 font-semibold">Rp ${formattedPrice}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Status:</span>
                                <span class="px-2 py-1 rounded-full text-xs ${statusClass}">${room.status.charAt(0).toUpperCase() + room.status.slice(1)}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-700">
                                <span class="text-gray-400">Capacity:</span>
                                <span class="text-white">${room.capacity} guests</span>
                            </div>
                    `;
                    
                    if (room.description) {
                        modalHtml += `
                            <div class="py-2">
                                <span class="text-gray-400">Description:</span>
                                <p class="text-white text-sm mt-1">${room.description}</p>
                            </div>
                        `;
                    }
                    
                    if (room.current_booking) {
                        modalHtml += `
                            <div class="mt-3 p-3 bg-yellow-500/10 rounded-lg">
                                <p class="text-yellow-500 text-sm font-semibold mb-2">Current Booking</p>
                                <p class="text-white text-sm">Guest: ${room.current_booking.guest_name}</p>
                                <p class="text-gray-400 text-xs">Check-in: ${room.current_booking.check_in}</p>
                                <p class="text-gray-400 text-xs">Check-out: ${room.current_booking.check_out}</p>
                            </div>
                        `;
                    }
                    
                    modalHtml += `</div>`;
                    
                    Swal.fire({
                        title: 'Room Details',
                        html: modalHtml,
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff',
                        width: '500px'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to load room details',
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMsg = 'Failed to load room details';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg,
                    confirmButtonColor: '#D4AF37',
                    background: '#1f2937',
                    color: '#fff'
                });
            }
        });
    };
    
    // Update statistics
    function updateStatistics() {
        const totalRows = $('#roomsTableBody tr:visible').length;
        const availableCount = $('#roomsTableBody tr:visible .status-select option:selected').filter(function() {
            return $(this).val() === 'available';
        }).length;
        const occupiedCount = $('#roomsTableBody tr:visible .status-select option:selected').filter(function() {
            return $(this).val() === 'occupied';
        }).length;
        const maintenanceCount = $('#roomsTableBody tr:visible .status-select option:selected').filter(function() {
            return $(this).val() === 'maintenance';
        }).length;
        
        $('#totalRooms').text(totalRows);
        $('#availableRooms').text(availableCount);
        $('#occupiedRooms').text(occupiedCount);
        $('#maintenanceRooms').text(maintenanceCount);
    }
    
    // Add room button loading state
    $('#addRoomBtn').on('click', function(e) {
        const btn = $(this);
        btn.addClass('opacity-50 cursor-not-allowed');
        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Loading...');
    });
</script>
@endpush