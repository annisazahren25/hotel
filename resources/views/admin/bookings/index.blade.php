@extends('admin.layouts.app')

@section('page_title', 'Booking Management')
@section('page_subtitle', 'Manage all hotel reservations')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Booking Management</h1>
            <p class="text-gray-400 text-sm mt-1">Manage and monitor all hotel reservations</p>
        </div>
        
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="text-center">
                <p class="text-gray-400 text-sm">Total</p>
                <p class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 bg-yellow-500/10 border border-yellow-500/30">
            <div class="text-center">
                <p class="text-yellow-400 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 bg-green-500/10 border border-green-500/30">
            <div class="text-center">
                <p class="text-green-400 text-sm">Confirmed</p>
                <p class="text-2xl font-bold text-green-400">{{ $stats['confirmed'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 bg-blue-500/10 border border-blue-500/30">
            <div class="text-center">
                <p class="text-blue-400 text-sm">Checked In</p>
                <p class="text-2xl font-bold text-blue-400">{{ $stats['checked_in'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 bg-purple-500/10 border border-purple-500/30">
            <div class="text-center">
                <p class="text-purple-400 text-sm">Checked Out</p>
                <p class="text-2xl font-bold text-purple-400">{{ $stats['checked_out'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="card rounded-xl p-4 bg-red-500/10 border border-red-500/30">
            <div class="text-center">
                <p class="text-red-400 text-sm">Cancelled</p>
                <p class="text-2xl font-bold text-red-400">{{ $stats['cancelled'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-gray-400 text-sm mb-1">Status</label>
                <select name="status" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Search</label>
                <input type="text" name="search" placeholder="Guest name or email" value="{{ request('search') }}" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/10">
                    <tr>
                        <th class="text-left p-3 text-gray-400">ID</th>
                        <th class="text-left p-3 text-gray-400">Guest</th>
                        <th class="text-left p-3 text-gray-400">Room</th>
                        <th class="text-left p-3 text-gray-400">Check In</th>
                        <th class="text-left p-3 text-gray-400">Check Out</th>
                        <th class="text-left p-3 text-gray-400">Subtotal</th>
                        <th class="text-left p-3 text-gray-400">Tax (12%)</th>
                        <th class="text-left p-3 text-gray-400">Total</th>
                        <th class="text-left p-3 text-gray-400">Status</th>
                        <th class="text-left p-3 text-gray-400">Cancellation</th>
                        <th class="text-left p-3 text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-b border-white/10">
                        <td class="p-3 text-gray-400">#{{ $booking->id }}</td>
                        <td class="p-3 text-white">{{ $booking->guest->name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-500">{{ $booking->guest->email ?? '' }}</span>
                        </td>
                        <td class="p-3 text-gray-300">
                            {{ $booking->room->roomType->name ?? 'N/A' }}<br>
                            <span class="text-xs text-gray-500">Room #{{ $booking->room->room_number ?? 'N/A' }}</span>
                        </td>
                        <td class="p-3 text-gray-300">
                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}<br>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->check_in_time ?? '14:00')->format('H:i') }} WIB</span>
                        </td>
                        <td class="p-3 text-gray-300">
                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}<br>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->check_out_time ?? '12:00')->format('H:i') }} WIB</span>
                        </td>
                        <td class="p-3 text-gray-300">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-300">Rp {{ number_format($booking->tax_amount ?? 0, 0, ',', '.') }}</td>
                        <td class="p-3 text-yellow-400 font-semibold">Rp {{ number_format($booking->grand_total ?? $booking->total_price, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <select onchange="updateStatus({{ $booking->id }}, this.value)" 
                                    class="px-2 py-1 rounded-full text-xs font-semibold cursor-pointer
                                @if($booking->status == 'pending') bg-yellow-500/20 text-yellow-500
                                @elseif($booking->status == 'confirmed') bg-green-500/20 text-green-500
                                @elseif($booking->status == 'checked_in') bg-blue-500/20 text-blue-500
                                @elseif($booking->status == 'checked_out') bg-purple-500/20 text-purple-500
                                @else bg-red-500/20 text-red-500 @endif">
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="checked_in" {{ $booking->status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                                <option value="checked_out" {{ $booking->status == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </td>
                        <td class="p-3">
                            @if($booking->cancellation_status == 'pending')
                                <div class="flex gap-1">
                                    <button onclick="approveCancellation({{ $booking->id }})" class="bg-green-500/20 text-green-500 px-2 py-1 rounded text-xs hover:bg-green-500/30">
                                        Approve
                                    </button>
                                    <button onclick="rejectCancellation({{ $booking->id }})" class="bg-red-500/20 text-red-500 px-2 py-1 rounded text-xs hover:bg-red-500/30">
                                        Reject
                                    </button>
                                </div>
                                <span class="text-xs text-yellow-500 mt-1 block">Refund: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span>
                            @elseif($booking->cancellation_status == 'approved' && !$booking->refund_processed_at)
                                <button onclick="processRefund({{ $booking->id }})" class="bg-blue-500/20 text-blue-500 px-2 py-1 rounded text-xs hover:bg-blue-500/30 w-full">
                                    Process Refund
                                </button>
                                <span class="text-xs text-green-500 mt-1 block">Refund: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span>
                            @elseif($booking->cancellation_status == 'approved' && $booking->refund_processed_at)
                                <span class="text-green-500 text-xs">Refunded</span>
                                <span class="text-xs text-gray-500 block">{{ \Carbon\Carbon::parse($booking->refund_processed_at)->format('d M Y') }}</span>
                            @elseif($booking->cancellation_status == 'rejected')
                                <span class="text-red-500 text-xs">Rejected</span>
                            @else
                                <span class="text-gray-500 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-blue-500 hover:text-blue-400" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($booking->payment_proof)
                                <a href="{{ asset($booking->payment_proof) }}" target="_blank" class="text-green-500 hover:text-green-400" title="Payment Proof">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-8 text-gray-400">
                            <i class="fas fa-calendar-times text-4xl mb-2"></i>
                            <p>No bookings found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</div>

<style>
    .card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Update booking status
    function updateStatus(bookingId, status) {
        Swal.fire({
            title: 'Update Status?',
            text: `Change booking status to ${status}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            confirmButtonText: 'Yes, update!',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/bookings/${bookingId}/status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                confirmButtonColor: '#D4AF37',
                                background: '#1f2937',
                                color: '#fff'
                            }).then(() => location.reload());
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update status',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    // Approve cancellation
    function approveCancellation(bookingId) {
        Swal.fire({
            title: 'Approve Cancellation?',
            text: 'Are you sure you want to approve this cancellation request?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Yes, approve!',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/cancellation/${bookingId}/approve`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            text: 'Cancellation approved. Refund will be processed.',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to approve cancellation',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    // Reject cancellation
    function rejectCancellation(bookingId) {
        Swal.fire({
            title: 'Reject Cancellation?',
            text: 'Are you sure you want to reject this cancellation request?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, reject!',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/cancellation/${bookingId}/reject`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Rejected!',
                            text: 'Cancellation request rejected.',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to reject cancellation',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    // Process refund
    function processRefund(bookingId) {
        Swal.fire({
            title: 'Process Refund?',
            text: 'Confirm that refund has been processed to customer.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Yes, processed!',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/cancellation/${bookingId}/refund`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Refund Processed!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to process refund',
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