@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('restaurant.menu') }}" class="text-gray-400 hover:text-yellow-500 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-white">My Orders</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-400">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-400">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                <div class="glass-card rounded-2xl p-6 hover:transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex flex-wrap justify-between items-start">
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <span class="text-gray-400 text-sm">Order #</span>
                                <span class="text-white font-semibold">{{ $order->order_number ?? 'ORD-' . $order->id }}</span>
                                
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg-yellow-500/20', 'text-yellow-500', 'fa-clock', 'Pending'],
                                        'preparing' => ['bg-blue-500/20', 'text-blue-500', 'fa-utensils', 'Preparing'],
                                        'ready' => ['bg-green-500/20', 'text-green-500', 'fa-check-circle', 'Ready'],
                                        'delivered' => ['bg-purple-500/20', 'text-purple-500', 'fa-truck', 'Delivered'],
                                        'paid' => ['bg-green-500/20', 'text-green-500', 'fa-credit-card', 'Paid'],
                                        'cancelled' => ['bg-red-500/20', 'text-red-500', 'fa-ban', 'Cancelled'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? ['bg-gray-500/20', 'text-gray-400', 'fa-question', ucfirst($order->status)];
                                @endphp
                                
                                <span class="px-3 py-1 rounded-full text-xs {{ $config[0] }} {{ $config[1] }}">
                                    <i class="fas {{ $config[2] }} mr-1"></i>
                                    {{ $config[3] }}
                                </span>
                            </div>
                            
                            <!-- Items -->
                            <div class="mb-3">
                                <p class="text-gray-400 text-sm mb-1">Items:</p>
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-white">{{ $item->menu_name ?? 'Menu Item' }} <span class="text-gray-400">x{{ $item->quantity }}</span></span>
                                        <span class="text-yellow-500">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Details -->
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-gray-400">Order Date:</p>
                                    <p class="text-white">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Total:</p>
                                    <p class="text-yellow-500 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                                @if($order->room_number)
                                <div>
                                    <p class="text-gray-400">Room:</p>
                                    <p class="text-white">#{{ $order->room_number }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 mt-4 md:mt-0 min-w-[140px]">
                            <a href="{{ route('restaurant.order.detail', $order->id) }}" 
                               class="bg-yellow-500 text-black px-4 py-2 rounded-lg text-sm font-semibold hover:bg-yellow-600 transition text-center">
                                <i class="fas fa-eye mr-2"></i> View Details
                            </a>
                            
                            @if($order->status == 'pending')
                            <a href="{{ route('payment.restaurant', $order->id) }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition text-center">
                                <i class="fas fa-credit-card mr-2"></i> Pay Now
                            </a>
                            <button onclick="cancelOrder({{ $order->id }})" 
                                    class="bg-red-500/20 text-red-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-500/30 transition text-center">
                                <i class="fas fa-times mr-2"></i> Cancel Order
                            </button>
                            @endif
                            
                            @if(in_array($order->status, ['preparing', 'ready']))
                            <a href="{{ route('restaurant.order.track', $order->id) }}" 
                               class="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500/30 transition text-center">
                                <i class="fas fa-map-marker-alt mr-2"></i> Track Order
                            </a>
                            @endif
                            
                            @if($order->status == 'paid')
                            <span class="bg-green-500/20 text-green-400 px-4 py-2 rounded-lg text-sm text-center">
                                <i class="fas fa-check-circle mr-2"></i> Completed
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="glass-card rounded-2xl p-12 text-center">
                <i class="fas fa-receipt text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Orders Yet</h3>
                <p class="text-gray-400 mb-6">You haven't placed any orders yet.</p>
                <a href="{{ route('restaurant.menu') }}" class="inline-block bg-yellow-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                    <i class="fas fa-utensils mr-2"></i> Browse Menu
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

<script>
    function cancelOrder(orderId) {
        Swal.fire({
            title: 'Cancel Order?',
            text: 'Are you sure you want to cancel this order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'No, Keep It',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937',
                    color: '#fff'
                });
                
                $.ajax({
                    url: `/restaurant/cancel/${orderId}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cancelled!',
                            text: 'Order cancelled successfully',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to cancel order',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection