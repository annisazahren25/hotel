@extends('admin.layouts.app')

@section('page_title', 'Restaurant Orders')
@section('page_subtitle', 'Manage all restaurant orders')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Restaurant Orders</h1>
            <p class="text-gray-400 text-sm mt-1">Manage and monitor all food orders</p>
        </div>
        
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        <div class="card rounded-xl p-3 text-center">
            <p class="text-gray-400 text-xs">Total</p>
            <p class="text-xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-yellow-500/10">
            <p class="text-yellow-400 text-xs">Ordered</p>
            <p class="text-xl font-bold text-yellow-400">{{ $stats['ordered'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-blue-500/10">
            <p class="text-blue-400 text-xs">Preparing</p>
            <p class="text-xl font-bold text-blue-400">{{ $stats['preparing'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-purple-500/10">
            <p class="text-purple-400 text-xs">Ready</p>
            <p class="text-xl font-bold text-purple-400">{{ $stats['ready'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-green-500/10">
            <p class="text-green-400 text-xs">Delivered</p>
            <p class="text-xl font-bold text-green-400">{{ $stats['delivered'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-teal-500/10">
            <p class="text-teal-400 text-xs">Paid</p>
            <p class="text-xl font-bold text-teal-400">{{ $stats['paid'] }}</p>
        </div>
        <div class="card rounded-xl p-3 text-center bg-red-500/10">
            <p class="text-red-400 text-xs">Cancelled</p>
            <p class="text-xl font-bold text-red-400">{{ $stats['cancelled'] }}</p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/10">
                    <tr>
                        <th class="text-left p-3 text-gray-400">ID</th>
                        <th class="text-left p-3 text-gray-400">Guest</th>
                        <th class="text-left p-3 text-gray-400">Type</th>
                        <th class="text-left p-3 text-gray-400">Items</th>
                        <th class="text-left p-3 text-gray-400">Subtotal</th>
                        <th class="text-left p-3 text-gray-400">Tax</th>
                        <th class="text-left p-3 text-gray-400">Total</th>
                        <th class="text-left p-3 text-gray-400">Status</th>
                        <th class="text-left p-3 text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="border-b border-white/10">
                        <td class="p-3 text-gray-400">#{{ $order->id }}</td>
                        <td class="p-3 text-white">{{ $order->guest->name ?? 'N/A' }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded-full {{ $order->order_type == 'dine_in' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                                {{ $order->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery' }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-300">{{ $order->items->count() }} items</td>
                        <td class="p-3 text-gray-300">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-300">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                        <td class="p-3 text-yellow-400 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <select class="status-select px-2 py-1 rounded-full text-xs font-semibold cursor-pointer
                                @if($order->status == 'ordered') bg-yellow-500/20 text-yellow-500
                                @elseif($order->status == 'preparing') bg-blue-500/20 text-blue-500
                                @elseif($order->status == 'ready') bg-purple-500/20 text-purple-500
                                @elseif($order->status == 'delivered') bg-green-500/20 text-green-500
                                @elseif($order->status == 'paid') bg-teal-500/20 text-teal-500
                                @else bg-red-500/20 text-red-500 @endif"
                                data-order-id="{{ $order->id }}">
                                <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('admin.restaurant.order.detail', $order->id) }}" class="text-blue-500 hover:text-blue-400">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-400">
                            <i class="fas fa-utensils text-4xl mb-2"></i>
                            <p>No orders found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>

@push('scripts')
<script>
    $('.status-select').on('change', function() {
        const orderId = $(this).data('order-id');
        const newStatus = $(this).val();
        const select = $(this);
        
        Swal.fire({
            title: 'Update Order Status?',
            text: `Change order status to ${newStatus}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            confirmButtonText: 'Update',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/restaurant/orders/${orderId}/status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            location.reload();
                        }
                    },
                    error: function() {
                        showError('Failed to update status');
                        location.reload();
                    }
                });
            } else {
                location.reload();
            }
        });
    });
</script>
@endpush
@endsection