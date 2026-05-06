@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('my.bookings') }}" class="text-gray-400 hover:text-yellow-500 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-white">Booking Details</h1>
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
        
        <div class="glass-card rounded-2xl p-6">
            <!-- Booking ID & Status -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-400">Booking ID</span>
                    <span class="text-white font-semibold">#{{ $booking->id }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-400">Status</span>
                    <span class="px-2 py-1 rounded-full text-xs {{ $booking->status_badge ?? 'bg-gray-500/20 text-gray-400' }}">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Booking Date</span>
                    <span class="text-white">{{ $booking->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
            
            <!-- Room Information -->
            <div class="border-t border-gray-700 pt-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-bed"></i> Room Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Room Type:</span>
                        <span class="text-white font-semibold">{{ $booking->room->roomType->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Room Number:</span>
                        <span class="text-white">#{{ $booking->room->room_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Floor:</span>
                        <span class="text-white">Floor {{ $booking->room->floor ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Capacity:</span>
                        <span class="text-white">{{ $booking->room->roomType->capacity ?? 2 }} guests</span>
                    </div>
                </div>
            </div>
            
            <!-- Booking Period -->
            <div class="border-t border-gray-700 pt-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i> Booking Period
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Check In:</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('l, d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Check In Time:</span>
                        <span class="text-white">14:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Check Out:</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('l, d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Check Out Time:</span>
                        <span class="text-white">12:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Nights:</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} nights</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Summary -->
            <div class="border-t border-gray-700 pt-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-credit-card"></i> Payment Summary
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Price per night:</span>
                        <span class="text-white">Rp {{ number_format($booking->room->roomType->price ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total for {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} nights:</span>
                        <span class="text-white">Rp {{ number_format($booking->room->roomType->price * \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold pt-2 border-t border-gray-700">
                        <span class="text-white text-lg">Total Payment:</span>
                        <span class="text-yellow-500 text-2xl font-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Status -->
            @if($booking->payment)
            <div class="border-t border-gray-700 pt-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-receipt"></i> Payment Status
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Method:</span>
                        <span class="text-white">{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Status:</span>
                        <span class="px-2 py-1 rounded-full text-xs {{ $booking->payment->status_badge ?? 'bg-yellow-500/20 text-yellow-500' }}">
                            {{ ucfirst($booking->payment->payment_status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Payment Date:</span>
                        <span class="text-white">{{ $booking->payment->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            @if($booking->status == 'pending')
            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-700">
                <a href="{{ route('payment.booking', $booking->id) }}" 
                   class="flex-1 bg-yellow-500 text-black text-center px-4 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i> Pay Now
                </a>
                <button onclick="cancelBooking({{ $booking->id }})" 
                        class="flex-1 bg-red-500/20 text-red-500 px-4 py-3 rounded-lg font-semibold hover:bg-red-500/30 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel Booking
                </button>
            </div>
            @elseif($booking->status == 'confirmed')
            <div class="flex gap-3 mt-6 pt-4 border-t border-gray-700">
                <button onclick="cancelBooking({{ $booking->id }})" 
                        class="w-full bg-red-500/20 text-red-500 px-4 py-3 rounded-lg font-semibold hover:bg-red-500/30 transition flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Cancel Booking
                </button>
            </div>
            @elseif($booking->status == 'checked_in')
            <div class="mt-6 pt-4 border-t border-gray-700">
                <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 text-center">
                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                    <p class="text-green-400 font-semibold">You are currently staying at our hotel</p>
                    <p class="text-gray-400 text-sm mt-1">Enjoy your stay! Room service is available 24/7</p>
                </div>
            </div>
            @elseif($booking->status == 'checked_out')
            <div class="mt-6 pt-4 border-t border-gray-700">
                <div class="bg-gray-500/20 border border-gray-500 rounded-lg p-4 text-center">
                    <i class="fas fa-history text-gray-400 text-2xl mb-2"></i>
                    <p class="text-gray-400 font-semibold">Thank you for staying with us</p>
                    <p class="text-gray-500 text-sm mt-1">We hope to see you again soon!</p>
                </div>
            </div>
            @elseif($booking->status == 'cancelled')
            <div class="mt-6 pt-4 border-t border-gray-700">
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 text-center">
                    <i class="fas fa-ban text-red-500 text-2xl mb-2"></i>
                    <p class="text-red-400 font-semibold">This booking has been cancelled</p>
                </div>
            </div>
            @endif
            
            <!-- Back Button -->
            <div class="mt-4">
                <a href="{{ route('my.bookings') }}" 
                   class="block w-full text-center text-gray-400 hover:text-white transition py-2">
                    <i class="fas fa-arrow-left mr-2"></i> Back to My Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border-color: rgba(212, 175, 55, 0.3);
    }
</style>

<script>
    function cancelBooking(bookingId) {
        Swal.fire({
            title: 'Cancel Booking?',
            html: `Are you sure you want to cancel this booking?<br><br>This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it',
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
                    url: `/bookings/${bookingId}/cancel`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelled!',
                                text: response.message || 'Booking cancelled successfully',
                                confirmButtonColor: '#D4AF37',
                                background: '#1f2937',
                                color: '#fff'
                            }).then(() => {
                                window.location.href = '{{ route("my.bookings") }}';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Failed to cancel booking',
                                confirmButtonColor: '#D4AF37',
                                background: '#1f2937',
                                color: '#fff'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'Failed to cancel booking';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    // Auto refresh if payment was just completed
    @if(session('success'))
    setTimeout(() => {
        location.reload();
    }, 2000);
    @endif
</script>
@endsection