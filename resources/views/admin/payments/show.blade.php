@extends('admin.layouts.app')

@section('page_title', 'Payment Details')
@section('page_subtitle', 'View complete payment information')

@section('content')
<div class="max-w-3xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.payments.index') }}" class="text-gray-400 hover:text-yellow-500">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Payment Details</h1>
            <p class="text-gray-400 text-sm">Payment ID: #{{ $payment->id }}</p>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-credit-card text-yellow-500"></i>
            Payment Information
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Amount:</span>
                <span class="text-2xl font-bold text-yellow-500">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Payment Method:</span>
                <span class="text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-1 rounded-full text-xs
                    @if($payment->payment_status == 'paid') bg-green-500/20 text-green-500
                    @elseif($payment->payment_status == 'pending') bg-yellow-500/20 text-yellow-500
                    @else bg-red-500/20 text-red-500 @endif">
                    {{ ucfirst($payment->payment_status) }}
                </span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Notes:</span>
                <span class="text-white">{{ $payment->note_text ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Payment Date:</span>
                <span class="text-white">{{ $payment->created_at->format('d F Y H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="card rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-receipt text-yellow-500"></i>
            Transaction Details
        </h3>
        
        @if($payment->booking_id)
            <div class="space-y-3">
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Transaction Type:</span>
                    <span class="text-blue-400">Room Booking Payment</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Guest Name:</span>
                    <span class="text-white">{{ $payment->booking->guest->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-white">Room {{ $payment->booking->room->room_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Check In:</span>
                    <span class="text-white">{{ $payment->booking->check_in_date ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Check Out:</span>
                    <span class="text-white">{{ $payment->booking->check_out_date ?? 'N/A' }}</span>
                </div>
            </div>
        @elseif($payment->restaurant_order_id)
            <div class="space-y-3">
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Transaction Type:</span>
                    <span class="text-purple-400">Restaurant Order Payment</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Guest Name:</span>
                    <span class="text-white">{{ $payment->restaurantOrder->guest->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-700 pb-2">
                    <span class="text-gray-400">Order Type:</span>
                    <span class="text-white">{{ $payment->restaurantOrder->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Order Items:</span>
                    <span class="text-white">{{ $payment->restaurantOrder->items->count() }} items</span>
                </div>
            </div>
        @endif
    </div>

    <div class="flex gap-3 mt-6">
        <a href="{{ route('admin.payments.index') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600">
            Back
        </a>
        @if($payment->payment_status == 'paid')
        <button onclick="refundPayment({{ $payment->id }})" class="bg-yellow-500/20 text-yellow-500 px-4 py-2 rounded-lg hover:bg-yellow-500/30">
            <i class="fas fa-undo-alt mr-2"></i> Refund
        </button>
        @endif
    </div>
</div>

<script>
function refundPayment(id) {
    Swal.fire({
        title: 'Process Refund?',
        text: 'Are you sure you want to refund this payment?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4AF37',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Refund',
        cancelButtonText: 'Cancel',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1f2937',
                color: '#fff'
            });
            
            $.ajax({
                url: `/admin/payments/${id}/refund`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Refund Processed!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                },
                error: function() {
                    Swal.close();
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