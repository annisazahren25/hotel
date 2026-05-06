@extends('admin.layouts.app')

@section('page_title', 'Guest Details')
@section('page_subtitle', 'View complete guest information')

@section('content')
<div class="max-w-4xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.guests.index') }}" class="text-gray-400 hover:text-yellow-500">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Guest Details</h1>
            <p class="text-gray-400 text-sm">Guest ID: #{{ $guest->id }}</p>
        </div>
    </div>

    <!-- Guest Information -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-user text-yellow-500"></i>
            Personal Information
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm">Full Name</p>
                <p class="text-white text-lg font-semibold">{{ $guest->name }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Email Address</p>
                <p class="text-white">{{ $guest->email }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Phone Number</p>
                <p class="text-white">{{ $guest->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Identity Number</p>
                <p class="text-white">{{ $guest->identity_number ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm">Address</p>
                <p class="text-white">{{ $guest->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Member Since</p>
                <p class="text-white">{{ $guest->created_at->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Role</p>
                <span class="px-2 py-1 rounded-full text-xs 
                    @if($guest->role == 'customer') bg-blue-500/20 text-blue-400
                    @elseif($guest->role == 'admin') bg-yellow-500/20 text-yellow-400
                    @else bg-green-500/20 text-green-400 @endif">
                    {{ ucfirst($guest->role) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Booking History -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-yellow-500"></i>
            Booking History ({{ $guest->bookings->count() }})
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2 text-gray-400">Booking ID</th>
                        <th class="text-left py-2 text-gray-400">Room</th>
                        <th class="text-left py-2 text-gray-400">Check In</th>
                        <th class="text-left py-2 text-gray-400">Check Out</th>
                        <th class="text-left py-2 text-gray-400">Total</th>
                        <th class="text-left py-2 text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guest->bookings as $booking)
                    <tr class="border-b border-gray-800">
                        <td class="py-2 text-white">#{{ $booking->id }}</td>
                        <td class="py-2 text-gray-300">Room {{ $booking->room->room_number ?? '-' }}</td>
                        <td class="py-2 text-gray-300">{{ $booking->check_in_date }}</td>
                        <td class="py-2 text-gray-300">{{ $booking->check_out_date }}</td>
                        <td class="py-2 text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($booking->status == 'pending') bg-yellow-500/20 text-yellow-500
                                @elseif($booking->status == 'confirmed') bg-green-500/20 text-green-500
                                @elseif($booking->status == 'checked_in') bg-blue-500/20 text-blue-500
                                @elseif($booking->status == 'checked_out') bg-gray-500/20 text-gray-400
                                @else bg-red-500/20 text-red-500 @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">No bookings found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Restaurant Orders -->
    <div class="card rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-utensils text-yellow-500"></i>
            Restaurant Orders ({{ $guest->restaurantOrders->count() }})
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2 text-gray-400">Order ID</th>
                        <th class="text-left py-2 text-gray-400">Type</th>
                        <th class="text-left py-2 text-gray-400">Items</th>
                        <th class="text-left py-2 text-gray-400">Total</th>
                        <th class="text-left py-2 text-gray-400">Status</th>
                        <th class="text-left py-2 text-gray-400">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guest->restaurantOrders as $order)
                    <tr class="border-b border-gray-800">
                        <td class="py-2 text-white">#{{ $order->id }}</td>
                        <td class="py-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $order->order_type == 'dine_in' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                                {{ $order->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery' }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-300">{{ $order->items->count() }} items</td>
                        <td class="py-2 text-yellow-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($order->status == 'ordered') bg-yellow-500/20 text-yellow-500
                                @elseif($order->status == 'preparing') bg-blue-500/20 text-blue-500
                                @elseif($order->status == 'ready') bg-purple-500/20 text-purple-500
                                @elseif($order->status == 'delivered') bg-green-500/20 text-green-500
                                @elseif($order->status == 'paid') bg-teal-500/20 text-teal-500
                                @else bg-red-500/20 text-red-500 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-2 text-gray-300">{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">No orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <a href="{{ route('admin.guests.index') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600">
            Back
        </a>
    </div>
</div>
@endsection