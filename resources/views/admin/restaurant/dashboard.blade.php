@extends('admin.layouts.app')

@section('page_title', 'Restaurant Dashboard')
@section('page_subtitle', 'Manage restaurant operations and orders')

@section('content')
<div class="fade-in">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
        <div class="card rounded-xl p-3 text-center">
            <p class="text-gray-400 text-xs">Total Orders</p>
            <p class="text-xl font-bold text-white">{{ $stats['total_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-yellow-500/10">
            <p class="text-yellow-400 text-xs">Pending</p>
            <p class="text-xl font-bold text-yellow-400">{{ $stats['pending_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-blue-500/10">
            <p class="text-blue-400 text-xs">Preparing</p>
            <p class="text-xl font-bold text-blue-400">{{ $stats['preparing_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-purple-500/10">
            <p class="text-purple-400 text-xs">Ready</p>
            <p class="text-xl font-bold text-purple-400">{{ $stats['ready_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-green-500/10">
            <p class="text-green-400 text-xs">Delivered</p>
            <p class="text-xl font-bold text-green-400">{{ $stats['delivered_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-teal-500/10">
            <p class="text-teal-400 text-xs">Paid</p>
            <p class="text-xl font-bold text-teal-400">{{ $stats['paid_orders'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center">
            <p class="text-gray-400 text-xs">Today Revenue</p>
            <p class="text-xl font-bold text-white">Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center">
            <p class="text-gray-400 text-xs">Total Revenue</p>
            <p class="text-xl font-bold text-gold-500">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-white font-semibold">Recent Orders</h3>
                <a href="{{ route('admin.restaurant.orders') }}" class="text-yellow-500 text-sm hover:underline">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentOrders ?? [] as $order)
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div>
                        <p class="text-white font-semibold">#{{ $order->id }} - {{ $order->guest->name ?? 'Guest' }}</p>
                        <p class="text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-yellow-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        <span class="text-xs px-2 py-1 rounded-full 
                            @if($order->status == 'ordered') bg-yellow-500/20 text-yellow-500
                            @elseif($order->status == 'preparing') bg-blue-500/20 text-blue-500
                            @elseif($order->status == 'ready') bg-purple-500/20 text-purple-500
                            @elseif($order->status == 'delivered') bg-green-500/20 text-green-500
                            @else bg-teal-500/20 text-teal-500 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">No recent orders</p>
                @endforelse
            </div>
        </div>

        <!-- Popular Items -->
        <div class="card rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-white font-semibold">Popular Items</h3>
                <a href="{{ route('admin.restaurant.menu') }}" class="text-yellow-500 text-sm hover:underline">Manage Menu →</a>
            </div>
            <div class="space-y-3">
                @forelse($popularItems ?? [] as $item)
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div>
                        <p class="text-white font-semibold">{{ $item->menu->name ?? 'Unknown' }}</p>
                        <p class="text-gray-400 text-xs">Sold: {{ $item->total_sold }}x</p>
                    </div>
                    <div class="text-right">
                        <p class="text-yellow-500">Rp {{ number_format(($item->menu->price ?? 0) * $item->total_sold, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">No order data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Order Type Stats -->
    <div class="grid grid-cols-2 gap-6 mt-6">
        <div class="card rounded-xl p-6 text-center">
            <i class="fas fa-utensils text-3xl gold-text mb-2"></i>
            <p class="text-gray-400 text-sm">Dine In</p>
            <p class="text-2xl font-bold text-white">{{ $orderTypeStats['dine_in'] ?? 0 }}</p>
        </div>
        <div class="card rounded-xl p-6 text-center">
            <i class="fas fa-truck text-3xl gold-text mb-2"></i>
            <p class="text-gray-400 text-sm">Room Delivery</p>
            <p class="text-2xl font-bold text-white">{{ $orderTypeStats['room_delivery'] ?? 0 }}</p>
        </div>
    </div>
</div>
@endsection