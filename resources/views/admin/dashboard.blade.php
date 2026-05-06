@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="font-serif text-4xl gold-text">Admin Dashboard</h1>
    <p class="text-gray-300 mt-2">Manage Hotel Operations</p>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass-card rounded-xl p-6">
        <i class="fas fa-bed text-3xl gold-text mb-3"></i>
        <h3 class="text-2xl font-bold">{{ $totalRooms ?? 0 }}</h3>
        <p class="text-gray-400">Total Rooms</p>
    </div>
    <div class="glass-card rounded-xl p-6">
        <i class="fas fa-calendar-check text-3xl gold-text mb-3"></i>
        <h3 class="text-2xl font-bold">{{ $pendingBookings ?? 0 }}</h3>
        <p class="text-gray-400">Pending Bookings</p>
    </div>
    <div class="glass-card rounded-xl p-6">
        <i class="fas fa-utensils text-3xl gold-text mb-3"></i>
        <h3 class="text-2xl font-bold">{{ $pendingOrders ?? 0 }}</h3>
        <p class="text-gray-400">Pending Orders</p>
    </div>
    <div class="glass-card rounded-xl p-6">
        <i class="fas fa-dollar-sign text-3xl gold-text mb-3"></i>
        <h3 class="text-2xl font-bold">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</h3>
        <p class="text-gray-400">Today's Revenue</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-serif text-2xl gold-text mb-4">Room Status</h2>
        <div class="space-y-3">
            @foreach($rooms ?? [] as $room)
            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                <div>
                    <span class="font-semibold">Room {{ $room->room_number }}</span>
                    <span class="text-sm text-gray-400 ml-2">{{ $room->roomType->name ?? '' }}</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs status-{{ $room->status }}">
                    {{ ucfirst($room->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="glass-card rounded-2xl p-6">
        <h2 class="font-serif text-2xl gold-text mb-4">Recent Orders</h2>
        <div class="space-y-3">
            @foreach($recentOrders ?? [] as $order)
            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                <div>
                    <span class="font-semibold">{{ $order->guest->name ?? 'Guest' }}</span>
                    <span class="text-sm text-gray-400 ml-2">{{ ucfirst($order->order_type) }}</span>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    <select onchange="updateOrderStatus({{ $order->id }}, this.value)" class="bg-white/10 rounded px-2 py-1 text-sm">
                        <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateOrderStatus(orderId, status) {
    $.ajax({
        url: `/admin/orders/${orderId}/status`,
        method: 'POST',
        data: {status: status, _token: '{{ csrf_token() }}'},
        success: function(response) {
            // Success
        }
    });
}
</script>
@endpush
@endsection