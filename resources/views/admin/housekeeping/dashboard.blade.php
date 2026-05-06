@extends('admin.layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Housekeeping Dashboard</h1>
    <p class="text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
    <p class="text-gold-500 text-sm mt-1">Role: Housekeeping - Manage room cleanliness</p>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <!-- Rooms to Clean Today -->
    <div class="card rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-broom text-2xl gold-text"></i>
            <h3 class="text-white font-semibold">Rooms to Clean Today</h3>
        </div>
        <p class="text-3xl font-bold text-white mb-2">{{ $todayCheckouts->count() }}</p>
        <p class="text-gray-400 text-sm">Check-outs today need cleaning</p>
    </div>
    
    <div class="card rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-exclamation-triangle text-2xl gold-text"></i>
            <h3 class="text-white font-semibold">Dirty/Maintenance Rooms</h3>
        </div>
        <p class="text-3xl font-bold text-white mb-2">{{ $dirtyRooms->count() }}</p>
        <p class="text-gray-400 text-sm">Require immediate attention</p>
    </div>
    
    <div class="card rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-check-circle text-2xl gold-text"></i>
            <h3 class="text-white font-semibold">Available Rooms</h3>
        </div>
        <p class="text-3xl font-bold text-white mb-2">{{ $rooms->where('status', 'available')->count() }}</p>
        <p class="text-gray-400 text-sm">Ready for check-in</p>
    </div>
</div>

<!-- Today's Check-outs -->
<div class="card rounded-xl p-6 mb-8">
    <h3 class="text-white font-semibold mb-4">Today's Check-outs (Need Cleaning)</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 text-gray-400">Room</th>
                    <th class="text-left py-3 text-gray-400">Guest</th>
                    <th class="text-left py-3 text-gray-400">Check-out Time</th>
                    <th class="text-left py-3 text-gray-400">Action</th>
                \)</thead>
            <tbody>
                @forelse($todayCheckouts as $booking)
                <tr class="border-b border-gray-800">
                    <td class="py-3 text-white font-semibold">Room {{ $booking->room->room_number }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->guest->name ?? 'N/A' }}</td>
                    <td class="py-3 text-gray-300">{{ \Carbon\Carbon::now()->format('H:i') }}</td>
                    <td class="py-3">
                        <button onclick="markForCleaning({{ $booking->room_id }})" class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded text-sm">
                            <i class="fas fa-broom mr-1"></i> Mark for Cleaning
                        </button>
                    </td>
                \)

                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-400">No check-outs today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- All Rooms Status -->
<div class="card rounded-xl p-6">
    <h3 class="text-white font-semibold mb-4">All Rooms Status</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($rooms as $room)
        <div class="p-3 rounded-lg text-center {{ $room->status == 'available' ? 'bg-green-500/20' : ($room->status == 'occupied' ? 'bg-blue-500/20' : 'bg-red-500/20') }}">
            <p class="font-bold text-white">Room {{ $room->room_number }}</p>
            <p class="text-xs mt-1 {{ $room->status == 'available' ? 'text-green-400' : ($room->status == 'occupied' ? 'text-blue-400' : 'text-red-400') }}">
                {{ ucfirst($room->status) }}
            </p>
            @if($room->status == 'maintenance')
            <button onclick="markClean({{ $room->id }})" class="mt-2 text-xs text-gold-500 underline">
                Mark Clean
            </button>
            @elseif($room->status == 'occupied')
            <button onclick="markDirty({{ $room->id }})" class="mt-2 text-xs text-gold-500 underline">
                Mark Dirty
            </button>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
function markForCleaning(roomId) {
    $.ajax({
        url: `/admin/housekeeping/rooms/${roomId}/dirty`,
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            location.reload();
        }
    });
}

function markClean(roomId) {
    $.ajax({
        url: `/admin/housekeeping/rooms/${roomId}/clean`,
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            location.reload();
        }
    });
}

function markDirty(roomId) {
    $.ajax({
        url: `/admin/housekeeping/rooms/${roomId}/dirty`,
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            location.reload();
        }
    });
}
</script>
@endpush