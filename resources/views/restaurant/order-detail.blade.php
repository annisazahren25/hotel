@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('restaurant.orders') }}" class="text-gray-400 hover:text-yellow-500 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-white">Order Details</h1>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <!-- Order Info -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Order Number:</span>
                    <span class="text-white font-semibold">{{ $order->order_number ?? 'ORD-' . $order->id }}</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Order Date:</span>
                    <span class="text-white">{{ $order->created_at->format('d F Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs
                        @if($order->status == 'pending') bg-yellow-500/20 text-yellow-500
                        @elseif($order->status == 'preparing') bg-blue-500/20 text-blue-500
                        @elseif($order->status == 'ready') bg-green-500/20 text-green-500
                        @elseif($order->status == 'delivered') bg-purple-500/20 text-purple-500
                        @elseif($order->status == 'paid') bg-green-500/20 text-green-500
                        @else bg-red-500/20 text-red-500 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                @if($order->room_number)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Room Number:</span>
                    <span class="text-white">#{{ $order->room_number }}</span>
                </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="border-t border-gray-700 pt-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3">Order Items</h3>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <div>
                            <span class="text-white">{{ $item->menu_name ?? 'Menu Item' }}</span>
                            <span class="text-gray-400 text-sm ml-2">x{{ $item->quantity }}</span>
                        </div>
                        <span class="text-yellow-500">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="border-t border-gray-700 pt-4">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Subtotal:</span>
                        <span class="text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tax (10%):</span>
                        <span class="text-white">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-700">
                        <span class="text-gray-400 font-semibold">Total:</span>
                        <span class="text-yellow-500 text-xl font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-4 border-t border-gray-700 flex gap-3">
                <a href="{{ route('restaurant.orders') }}" class="flex-1 bg-gray-700 text-gray-300 px-4 py-2 rounded-lg text-center hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                @if($order->status == 'pending')
                <a href="{{ route('payment.restaurant', $order->id) }}" class="flex-1 bg-yellow-500 text-black px-4 py-2 rounded-lg text-center font-semibold hover:bg-yellow-600 transition">
                    <i class="fas fa-credit-card mr-2"></i> Pay Now
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endsection