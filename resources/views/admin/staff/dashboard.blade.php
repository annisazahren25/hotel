@extends('admin.layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Staff Dashboard</h1>
    <p class="text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
    <p class="text-gold-500 text-sm mt-1">Role: Staff - Manage daily operations</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="card rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 gold-bg rounded-lg flex items-center justify-center">
                <i class="fas fa-bed text-black text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $totalRooms ?? 0 }}</span>
        </div>
        <p class="text-gray-400 text-sm">Total Rooms</p>
    </div>
    
    <div class="card rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 gold-bg rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-black text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $todayCheckins ?? 0 }}</span>
        </div>
        <p class="text-gray-400 text-sm">Today's Check-ins</p>
    </div>
    
    <div class="card rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 gold-bg rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-times text-black text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $todayCheckouts ?? 0 }}</span>
        </div>
        <p class="text-gray-400 text-sm">Today's Check-outs</p>
    </div>
    
    <div class="card rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 gold-bg rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-black text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $pendingServices ?? 0 }}</span>
        </div>
        <p class="text-gray-400 text-sm">Pending Services</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="card rounded-xl p-6">
        <h3 class="text-white font-semibold mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.bookings.index') }}" class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition">
                <i class="fas fa-calendar-plus text-2xl gold-text mb-2"></i>
                <p class="text-sm">New Booking</p>
            </a>
            <a href="{{ route('admin.bookings.index') }}?status=checked_in" class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition">
                <i class="fas fa-user-check text-2xl gold-text mb-2"></i>
                <p class="text-sm">Check-in Guest</p>
            </a>
            <a href="{{ route('admin.bookings.index') }}?status=checked_out" class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition">
                <i class="fas fa-user-minus text-2xl gold-text mb-2"></i>
                <p class="text-sm">Check-out Guest</p>
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition">
                <i class="fas fa-bed text-2xl gold-text mb-2"></i>
                <p class="text-sm">Update Room Status</p>
            </a>
        </div>
    </div>
    
    <div class="card rounded-xl p-6">
        <h3 class="text-white font-semibold mb-4">Today's Schedule</h3>
        <div class="space-y-3">
            @forelse($todaySchedule ?? [] as $schedule)
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div>
                    <p class="text-white font-semibold">{{ $schedule->guest->name ?? 'N/A' }}</p>
                    <p class="text-gray-400 text-xs">Room {{ $schedule->room->room_number ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gold-500 text-sm">{{ \Carbon\Carbon::parse($schedule->check_in_date)->format('H:i') }}</p>
                    <span class="status-badge status-{{ $schedule->status }} text-xs">{{ ucfirst($schedule->status) }}</span>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">No schedule for today</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="card rounded-xl p-6">
    <h3 class="text-white font-semibold mb-4">Recent Bookings</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 text-gray-400">Guest</th>
                    <th class="text-left py-3 text-gray-400">Room</th>
                    <th class="text-left py-3 text-gray-400">Check-in</th>
                    <th class="text-left py-3 text-gray-400">Check-out</th>
                    <th class="text-left py-3 text-gray-400">Status</th>
                    <th class="text-left py-3 text-gray-400">Action</th>
                \)
            </thead>
            <tbody>
                @forelse($recentBookings ?? [] as $booking)
                <tr class="border-b border-gray-800">
                    <td class="py-3 text-white">{{ $booking->guest->name ?? 'N/A' }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->room->room_number ?? 'N/A' }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->check_in_date }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->check_out_date }}</td>
                    <td class="py-3">
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </td>
                    <td class="py-3">
                        @if($booking->status == 'confirmed')
                        <button onclick="checkIn({{ $booking->id }})" class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs">
                            Check In
                        </button>
                        @elseif($booking->status == 'checked_in')
                        <button onclick="checkOut({{ $booking->id }})" class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs">
                            Check Out
                        </button>
                        @endif
                    </td>
                \)

                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-400">No bookings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkIn(bookingId) {
    $.ajax({
        url: `/admin/bookings/${bookingId}/checkin`,
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            location.reload();
        }
    });
}

function checkOut(bookingId) {
    $.ajax({
        url: `/admin/bookings/${bookingId}/checkout`,
        method: 'POST',
        data: {_token: '{{ csrf_token() }}'},
        success: function(response) {
            location.reload();
        }
    });
}
</script>
@endpush