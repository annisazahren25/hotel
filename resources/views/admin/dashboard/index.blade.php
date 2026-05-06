@extends('admin.layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Dashboard</h1>
    <p class="text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-bed text-yellow-500 text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $totalRooms }}</span>
        </div>
        <p class="text-gray-400 text-sm">Total Rooms</p>
        <div class="flex gap-2 mt-2 text-xs">
            <span class="text-green-500">Available: {{ $availableRooms }}</span>
            <span class="text-blue-500">Occupied: {{ $occupiedRooms }}</span>
        </div>
    </div>
    
    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check text-yellow-500 text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $totalBookings }}</span>
        </div>
        <p class="text-gray-400 text-sm">Total Bookings</p>
        <div class="text-xs text-yellow-500 mt-2">Pending: {{ $pendingBookings }}</div>
    </div>
    
    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-yellow-500 text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">${{ number_format($todayRevenue, 0) }}</span>
        </div>
        <p class="text-gray-400 text-sm">Today's Revenue</p>
        <div class="text-xs text-gray-500 mt-2">Month: ${{ number_format($monthRevenue, 0) }}</div>
    </div>
    
    <div class="bg-white/5 rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-500 text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-white">{{ $occupancyRate }}%</span>
        </div>
        <p class="text-gray-400 text-sm">Occupancy Rate</p>
        <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $occupancyRate }}%"></div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white/5 rounded-xl p-6 border border-white/10 mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-white font-semibold">Recent Bookings</h3>
        <a href="{{ route('admin.bookings.index') }}" class="text-yellow-500 text-sm hover:underline">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 text-gray-400">Guest</th>
                    <th class="text-left py-3 text-gray-400">Room</th>
                    <th class="text-left py-3 text-gray-400">Check In</th>
                    <th class="text-left py-3 text-gray-400">Check Out</th>
                    <th class="text-left py-3 text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                <tr class="border-b border-gray-800">
                    <td class="py-3 text-white">{{ $booking->guest->name ?? 'N/A' }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->room->room_number ?? 'N/A' }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->check_in_date }}</td>
                    <td class="py-3 text-gray-300">{{ $booking->check_out_date }}</td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-full text-xs 
                            @if($booking->status == 'pending') bg-yellow-500/20 text-yellow-500
                            @elseif($booking->status == 'confirmed') bg-green-500/20 text-green-500
                            @elseif($booking->status == 'checked_in') bg-blue-500/20 text-blue-500
                            @elseif($booking->status == 'checked_out') bg-gray-500/20 text-gray-400
                            @else bg-red-500/20 text-red-500
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-400">No bookings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white/5 rounded-xl p-6 border border-white/10">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-white font-semibold">Recent Restaurant Orders</h3>
        <a href="{{ route('admin.restaurant.orders') }}" class="text-yellow-500 text-sm hover:underline">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-700">
                    <th class="text-left py-3 text-gray-400">Guest</th>
                    <th class="text-left py-3 text-gray-400">Type</th>
                    <th class="text-left py-3 text-gray-400">Total</th>
                    <th class="text-left py-3 text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr class="border-b border-gray-800">
                    <td class="py-3 text-white">{{ $order->guest->name ?? 'N/A' }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $order->order_type == 'dine_in' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                            {{ $order->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery' }}
                        </span>
                    </td>
                    <td class="py-3 text-white">${{ number_format($order->total_price, 0) }}</td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-full text-xs bg-yellow-500/20 text-yellow-500">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-400">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection