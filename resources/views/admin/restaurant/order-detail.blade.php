@extends('admin.layouts.app')

@section('page_title', 'Order Details')
@section('page_subtitle', 'View complete order information')

@section('content')
<div class="max-w-3xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.restaurant.orders') }}" class="text-gray-400 hover:text-yellow-500">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Order Details</h1>
            <p class="text-gray-400 text-sm">Order ID: #{{ $order->id }}</p>
        </div>
    </div>

    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Customer Information</h3>
        <div class="space-y-2">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Name:</span>
                <span class="text-white">{{ $order->guest->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Email:</span>
                <span class="text-white">{{ $order->guest->email ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Phone:</span>
                <span class="text-white">{{ $order->guest->phone ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Order Items</h3>
        <div class="space-y-3">
            @foreach($order->items as $item)
            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                <div>
                    <p class="text-white font-semibold">{{ $item->menu->name }}</p>
                    <p class="text-gray-400 text-sm">Qty: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
                <p class="text-yellow-500">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Payment Summary</h3>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-400">Subtotal:</span>
                <span class="text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Tax (12%):</span>
                <span class="text-white">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
            @if($order->delivery_fee > 0)
            <div class="flex justify-between">
                <span class="text-gray-400">Delivery Fee:</span>
                <span class="text-white">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between pt-2 border-t border-gray-700">
                <span class="text-lg font-semibold text-white">Total:</span>
                <span class="text-xl font-bold text-yellow-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <a href="{{ route('admin.restaurant.orders') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600">
            Back
        </a>
    </div>
</div>
@endsection