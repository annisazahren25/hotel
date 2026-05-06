@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">My Bookings</h1>
                <p class="text-gray-400">View and manage all your room bookings</p>
            </div>
            <a href="{{ route('rooms.index') }}" 
               class="bg-yellow-500 text-black px-4 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                New Booking
            </a>
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
        
        @if($bookings->count() > 0)
            <!-- Statistics Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-800/50 rounded-xl p-3 text-center">
                    <p class="text-gray-400 text-xs">Total Bookings</p>
                    <p class="text-2xl font-bold text-white">{{ $bookings->count() }}</p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-3 text-center">
                    <p class="text-gray-400 text-xs">Active</p>
                    <p class="text-2xl font-bold text-green-400">
                        {{ $bookings->whereIn('status', ['pending', 'confirmed', 'checked_in'])->count() }}
                    </p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-3 text-center">
                    <p class="text-gray-400 text-xs">Completed</p>
                    <p class="text-2xl font-bold text-blue-400">
                        {{ $bookings->where('status', 'checked_out')->count() }}
                    </p>
                </div>
                <div class="bg-gray-800/50 rounded-xl p-3 text-center">
                    <p class="text-gray-400 text-xs">Cancelled</p>
                    <p class="text-2xl font-bold text-red-400">
                        {{ $bookings->where('status', 'cancelled')->count() }}
                    </p>
                </div>
            </div>
        
            <div class="space-y-4">
                @foreach($bookings as $booking)
                <div class="glass-card rounded-2xl p-6 hover:transform hover:scale-[1.02] transition-all duration-300" id="booking-{{ $booking->id }}">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                <span class="text-gray-400 text-sm">Booking ID:</span>
                                <span class="text-white font-semibold">#{{ $booking->id }}</span>
                                
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg-yellow-500/20', 'text-yellow-400', 'fa-clock', 'Pending Payment'],
                                        'confirmed' => ['bg-blue-500/20', 'text-blue-400', 'fa-check-circle', 'Confirmed'],
                                        'checked_in' => ['bg-green-500/20', 'text-green-400', 'fa-bed', 'Checked In'],
                                        'checked_out' => ['bg-gray-500/20', 'text-gray-400', 'fa-check-double', 'Completed'],
                                        'cancelled' => ['bg-red-500/20', 'text-red-400', 'fa-ban', 'Cancelled'],
                                        'cancellation_requested' => ['bg-orange-500/20', 'text-orange-400', 'fa-clock', 'Cancellation Requested'],
                                    ];
                                    $config = $statusConfig[$booking->status] ?? ['bg-gray-500/20', 'text-gray-400', 'fa-question', ucfirst($booking->status)];
                                @endphp
                                
                                <span class="px-3 py-1 rounded-full text-xs {{ $config[0] }} {{ $config[1] }}">
                                    <i class="fas {{ $config[2] }} mr-1"></i>
                                    {{ $config[3] }}
                                </span>
                                
                                @if($booking->status == 'pending')
                                <span class="px-2 py-1 rounded-full text-xs bg-red-500/20 text-red-400 animate-pulse">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Action Required
                                </span>
                                @endif
                                
                                @if($booking->status == 'cancellation_requested')
                                <span class="px-2 py-1 rounded-full text-xs bg-orange-500/20 text-orange-400">
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    Awaiting Admin Approval
                                </span>
                                @endif
                            </div>
                            
                            <!-- Refund Info if cancelled -->
                            @if($booking->status == 'cancelled' && $booking->refund_amount > 0)
                            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-green-500"></i>
                                    <span class="text-green-400 text-sm">Refund processed: </span>
                                    <span class="text-green-500 font-semibold">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Details Grid -->
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-hotel mr-1"></i> Room Type
                                    </p>
                                    <p class="text-white font-semibold">{{ $booking->room->roomType->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-hashtag mr-1"></i> Room Number
                                    </p>
                                    <p class="text-white">#{{ $booking->room->room_number ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-calendar-check mr-1"></i> Booking Date
                                    </p>
                                    <p class="text-white">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Check In
                                    </p>
                                    <p class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">14:00 WIB</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Check Out
                                    </p>
                                    <p class="text-white">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">12:00 WIB</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-moon mr-1"></i> Nights
                                    </p>
                                    <p class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} night(s)</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-users mr-1"></i> Guests
                                    </p>
                                    <p class="text-white">{{ $booking->guests ?? 2 }} person(s)</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-gray-400 text-sm mb-1">
                                        <i class="fas fa-tag mr-1"></i> Total Price
                                    </p>
                                    <p class="text-yellow-500 font-bold text-xl">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2 min-w-[160px]">
                            @if($booking->status == 'pending')
                                <a href="{{ route('payment.booking', $booking->id) }}" 
                                   class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-4 py-2.5 rounded-lg text-sm font-semibold hover:from-yellow-600 hover:to-yellow-700 transition text-center shadow-lg">
                                    <i class="fas fa-credit-card mr-2"></i> Complete Payment
                                </a>
                                <button onclick="requestCancellation({{ $booking->id }})" 
                                        class="bg-red-500/20 text-red-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-500/30 transition text-center border border-red-500/30">
                                    <i class="fas fa-times mr-2"></i> Request Cancellation
                                </button>
                                
                            @elseif($booking->status == 'confirmed')
                                <div class="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg text-sm text-center">
                                    <i class="fas fa-check-circle mr-2"></i> Payment Confirmed
                                </div>
                                <button onclick="requestCancellation({{ $booking->id }})" 
                                        class="bg-red-500/20 text-red-500 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-500/30 transition text-center">
                                    <i class="fas fa-times mr-2"></i> Request Cancellation
                                </button>
                                
                            @elseif($booking->status == 'cancellation_requested')
                                <div class="bg-orange-500/20 text-orange-400 px-4 py-2 rounded-lg text-sm text-center">
                                    <i class="fas fa-hourglass-half mr-2"></i> Cancellation Requested
                                </div>
                                <div class="text-gray-500 text-xs text-center mt-1">
                                    Waiting for admin approval
                                </div>
                                
                            @elseif($booking->status == 'checked_in')
                                <div class="bg-green-500/20 text-green-400 px-4 py-2 rounded-lg text-sm text-center">
                                    <i class="fas fa-bed mr-2"></i> Currently Staying
                                </div>
                                <span class="text-gray-500 text-xs text-center">Enjoy your stay!</span>
                                
                            @elseif($booking->status == 'checked_out')
                                <div class="bg-gray-500/20 text-gray-400 px-4 py-2 rounded-lg text-sm text-center">
                                    <i class="fas fa-check-double mr-2"></i> Stay Completed
                                </div>
                                <a href="{{ route('rooms.index') }}" class="text-blue-400 hover:text-blue-300 text-sm text-center">
                                    <i class="fas fa-redo mr-1"></i> Book Again
                                </a>
                                
                            @elseif($booking->status == 'cancelled')
                                <div class="bg-red-500/20 text-red-400 px-4 py-2 rounded-lg text-sm text-center">
                                    <i class="fas fa-ban mr-2"></i> Booking Cancelled
                                </div>
                                @if($booking->refund_amount > 0)
                                <div class="text-green-500 text-xs text-center">
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    Refunded: Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}
                                </div>
                                @endif
                                <a href="{{ route('rooms.index') }}" class="text-blue-400 hover:text-blue-300 text-sm text-center">
                                    <i class="fas fa-search mr-1"></i> Browse Rooms
                                </a>
                            @endif
                            
                            <a href="{{ route('booking.show', $booking->id) }}" 
                               class="text-blue-400 hover:text-blue-300 text-sm text-center mt-2 transition">
                                <i class="fas fa-eye mr-1"></i> View Full Details
                            </a>
                        </div>
                    </div>
                    
                    <!-- Cancellation Reason if requested -->
                    @if($booking->status == 'cancellation_requested' && $booking->cancellation_reason)
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-gray-400 text-sm">
                            <i class="fas fa-comment mr-1"></i> Cancellation reason: 
                            <span class="text-gray-300">{{ $booking->cancellation_reason }}</span>
                        </p>
                        @if($booking->refund_amount > 0)
                        <p class="text-green-400 text-sm mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Estimated refund: 
                            <strong>Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</strong>
                        </p>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Additional Info for Checked In -->
                    @if($booking->status == 'checked_in')
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <span class="text-gray-400">
                                <i class="fas fa-concierge-bell mr-1"></i> Room service: Ext. 1234
                            </span>
                            <span class="text-gray-400">
                                <i class="fas fa-wifi mr-1"></i> WiFi: HotelGuest / hotel123
                            </span>
                            <span class="text-gray-400">
                                <i class="fas fa-phone mr-1"></i> Reception: Ext. 0
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(method_exists($bookings, 'links'))
            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
            @endif
        @else
            <div class="glass-card rounded-2xl p-12 text-center">
                <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-4xl text-gray-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No Bookings Yet</h3>
                <p class="text-gray-400 mb-6">You haven't made any bookings yet. Start exploring our rooms!</p>
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 bg-yellow-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                    <i class="fas fa-search"></i>
                    Browse Available Rooms
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
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border-color: rgba(212, 175, 55, 0.3);
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<script>
    function requestCancellation(bookingId) {
        // First, fetch cancellation details to show refund policy
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: '#1f2937',
            color: '#fff'
        });
        
        $.ajax({
            url: `/bookings/${bookingId}/cancellation-details`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    const data = response.data;
                    const formattedRefund = data.formatted_refund;
                    const policyText = data.cancellation_policy;
                    
                    Swal.fire({
                        title: 'Request Cancellation?',
                        html: `
                            <div class="text-left">
                                <p class="mb-3 text-gray-300">Are you sure you want to cancel this booking?</p>
                                
                                <div class="bg-yellow-500/10 rounded-lg p-3 mb-3">
                                    <p class="text-yellow-400 text-sm font-semibold mb-2">📋 Cancellation Policy:</p>
                                    <p class="text-gray-300 text-sm">${policyText}</p>
                                    ${data.refund_amount > 0 ? 
                                        `<p class="text-green-400 text-sm mt-2">💰 Estimated refund: <strong>${formattedRefund}</strong></p>` : 
                                        `<p class="text-red-400 text-sm mt-2">⚠️ No refund will be issued for this cancellation.</p>`
                                    }
                                </div>
                                
                                <div class="bg-blue-500/10 rounded-lg p-3 mb-3">
                                    <p class="text-blue-400 text-sm">ℹ️ Booking ID: <strong>#${data.booking_id}</strong></p>
                                    <p class="text-blue-400 text-sm">Total paid: <strong>Rp ${new Intl.NumberFormat('id-ID').format(data.total_price)}</strong></p>
                                    <p class="text-gray-400 text-xs mt-1">Days until check-in: ${data.days_until_check_in} days</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="text-gray-300 text-sm block mb-2">Reason for cancellation (optional):</label>
                                    <textarea id="cancellationReason" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm" 
                                        placeholder="e.g., Change of plans, Found cheaper hotel, Emergency, etc."></textarea>
                                </div>
                                
                                <div class="bg-red-500/10 rounded-lg p-2 mt-2">
                                    <p class="text-red-400 text-xs text-center">⚠️ This action cannot be undone. Admin approval is required.</p>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Request Cancellation',
                        cancelButtonText: 'No, Keep It',
                        background: '#1f2937',
                        color: '#fff',
                        width: '550px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const reason = document.getElementById('cancellationReason')?.value || '';
                            
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Submitting your cancellation request',
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
                                    _token: '{{ csrf_token() }}',
                                    reason: reason
                                },
                                dataType: 'json',
                                success: function(response) {
                                    Swal.close();
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Request Submitted!',
                                            html: response.message,
                                            confirmButtonColor: '#D4AF37',
                                            background: '#1f2937',
                                            color: '#fff'
                                        }).then(() => {
                                            location.reload();
                                        });
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
                                error: function(xhr) {
                                    Swal.close();
                                    let errorMsg = 'Failed to request cancellation';
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to load cancellation details',
                        confirmButtonColor: '#D4AF37',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load cancellation details. Please try again.',
                    confirmButtonColor: '#D4AF37',
                    background: '#1f2937',
                    color: '#fff'
                });
            }
        });
    }
    
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff',
        timer: 3000,
        showConfirmButton: true
    });
    @endif
    
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: "{{ session('error') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff'
    });
    @endif
    
    @if(session('info'))
    Swal.fire({
        icon: 'info',
        title: 'Information',
        text: "{{ session('info') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff'
    });
    @endif
</script>
@endsection