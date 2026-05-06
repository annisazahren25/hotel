@extends('layouts.app')

@section('title', 'Cash Payment - Hotel Bahagia')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-money-bill-wave text-4xl text-green-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Cash Payment Instructions</h1>
                <p class="text-gray-400 mt-2">Please follow the instructions below to complete your payment</p>
            </div>

            <!-- Booking Summary -->
            <div class="bg-white/5 rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Booking ID:</span>
                    <span class="text-white font-semibold">#{{ $booking->id }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-white">{{ $booking->room->roomType->name ?? 'N/A' }} (#{{ $booking->room->room_number ?? 'N/A' }})</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Check In:</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-400">Check Out:</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-700 mt-2">
                    <span class="text-white font-semibold">Total Amount:</span>
                    <span class="text-2xl font-bold text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="space-y-4 mb-6">
                <div class="flex items-start gap-3 p-3 bg-green-500/10 rounded-lg">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-500 font-bold">1</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Go to Hotel Reception</p>
                        <p class="text-gray-400 text-sm">Visit our front desk located at the hotel lobby. Our reception is open 24/7.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-blue-500/10 rounded-lg">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-500 font-bold">2</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Provide Your Booking ID</p>
                        <p class="text-gray-400 text-sm">Show or tell the receptionist your Booking ID: <strong class="text-yellow-500">#{{ $booking->id }}</strong></p>
                        <button onclick="copyBookingId()" class="text-xs text-yellow-500 hover:text-yellow-400 mt-1">
                            <i class="fas fa-copy mr-1"></i> Copy Booking ID
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-yellow-500/10 rounded-lg">
                    <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-yellow-500 font-bold">3</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Pay the Amount</p>
                        <p class="text-gray-400 text-sm">Pay the total amount of <strong class="text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong> in cash.</p>
                        <p class="text-gray-500 text-xs mt-1">💡 Tip: Prepare exact change if possible.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-purple-500/10 rounded-lg">
                    <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-purple-500 font-bold">4</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Get Confirmation</p>
                        <p class="text-gray-400 text-sm">The receptionist will confirm your payment and check you in.</p>
                        <p class="text-gray-500 text-xs mt-1">You will receive a receipt as proof of payment.</p>
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-yellow-500 mt-0.5"></i>
                    <div>
                        <p class="text-yellow-400 font-semibold">Booking Status: Pending</p>
                        <p class="text-gray-400 text-sm">Your booking is currently <strong>PENDING</strong>. After you complete the payment at the hotel, the receptionist will update your booking status to <strong>CONFIRMED</strong>.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <form action="{{ route('payment.cash.process', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" id="confirmBtn" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> I Have Paid at Reception
                    </button>
                </form>
                
                <a href="{{ route('my.bookings') }}" class="text-center text-gray-400 hover:text-white transition py-2">
                    <i class="fas fa-arrow-left mr-2"></i> Back to My Bookings
                </a>
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

<script>
    function copyBookingId() {
        const bookingId = '{{ $booking->id }}';
        navigator.clipboard.writeText(bookingId);
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Booking ID #' + bookingId + ' copied to clipboard',
            timer: 1500,
            showConfirmButton: false,
            background: '#1f2937',
            color: '#fff'
        });
    }

    document.getElementById('confirmBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Confirm Payment',
            html: `Have you completed the cash payment at the hotel reception?<br><br>
                   <div class="bg-yellow-500/10 rounded-lg p-3 mt-2">
                       <p class="text-yellow-400">Booking ID: <strong>#{{ $booking->id }}</strong></p>
                       <p class="text-yellow-400">Amount: <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Payment Completed',
            cancelButtonText: 'Not Yet',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = document.getElementById('confirmBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                btn.disabled = true;
                
                // Submit the form
                document.querySelector('form').submit();
            }
        });
    });
</script>
@endsection