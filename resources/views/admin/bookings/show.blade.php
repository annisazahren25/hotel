@extends('admin.layouts.app')

@section('page_title', 'Booking Details')
@section('page_subtitle', 'View complete booking information')

@section('content')
<div class="max-w-4xl mx-auto fade-in">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="text-gray-400 hover:text-yellow-500">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">Booking Details</h1>
            <p class="text-gray-400 text-sm">Booking ID: #{{ $booking->id }}</p>
        </div>
    </div>

    <!-- Guest Information -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-user mr-2 text-yellow-500"></i> Guest Information
        </h3>
        <div class="space-y-2">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Name:</span>
                <span class="text-white">{{ $booking->guest->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Email:</span>
                <span class="text-white">{{ $booking->guest->email ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Phone:</span>
                <span class="text-white">{{ $booking->guest->phone ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Room Information -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-bed mr-2 text-yellow-500"></i> Room Information
        </h3>
        <div class="space-y-2">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Room Number:</span>
                <span class="text-white">#{{ $booking->room->room_number ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Room Type:</span>
                <span class="text-white">{{ $booking->room->roomType->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Floor:</span>
                <span class="text-white">Lantai {{ $booking->room->floor ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Price per Night:</span>
                <span class="text-yellow-400">Rp {{ number_format($booking->room->roomType->price ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-calendar-alt mr-2 text-yellow-500"></i> Booking Details
        </h3>
        <div class="space-y-2">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Check In:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Check Out:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Total Nights:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} nights</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Guests:</span>
                <span class="text-white">{{ $booking->guests ?? 2 }} person(s)</span>
            </div>
            @if($booking->special_requests)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Special Requests:</span>
                <span class="text-white">{{ $booking->special_requests }}</span>
            </div>
            @endif
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Total Price:</span>
                <span class="text-yellow-400 text-xl font-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-1 rounded-full text-xs
                    @if($booking->status == 'pending') bg-yellow-500/20 text-yellow-500
                    @elseif($booking->status == 'confirmed') bg-green-500/20 text-green-500
                    @elseif($booking->status == 'checked_in') bg-blue-500/20 text-blue-500
                    @elseif($booking->status == 'checked_out') bg-gray-500/20 text-gray-400
                    @elseif($booking->status == 'cancelled') bg-red-500/20 text-red-500
                    @elseif($booking->status == 'cancellation_requested') bg-orange-500/20 text-orange-500
                    @else bg-gray-500/20 text-gray-400 @endif">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-credit-card mr-2 text-yellow-500"></i> Payment Information
        </h3>
        
        @php
            $payment = $booking->payment;
        @endphp
        
        @if($payment)
        <div class="space-y-2">
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Payment Method:</span>
                <span class="text-white">
                    @if($payment->payment_method == 'cash')
                        <i class="fas fa-money-bill-wave text-green-500 mr-1"></i> Cash
                    @elseif($payment->payment_method == 'transfer')
                        <i class="fas fa-university text-blue-500 mr-1"></i> Bank Transfer
                    @elseif($payment->payment_method == 'credit_card')
                        <i class="fab fa-cc-visa text-purple-500 mr-1"></i> Credit Card
                    @elseif($payment->payment_method == 'e_wallet')
                        <i class="fas fa-mobile-alt text-teal-500 mr-1"></i> E-Wallet
                    @else
                        {{ ucfirst($payment->payment_method) }}
                    @endif
                </span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Payment Status:</span>
                <span class="px-2 py-1 rounded-full text-xs
                    @if($payment->payment_status == 'paid') bg-green-500/20 text-green-500
                    @elseif($payment->payment_status == 'pending') bg-yellow-500/20 text-yellow-500
                    @elseif($payment->payment_status == 'refunded') bg-orange-500/20 text-orange-500
                    @else bg-red-500/20 text-red-500 @endif">
                    {{ ucfirst($payment->payment_status) }}
                </span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Amount Paid:</span>
                <span class="text-green-400 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Payment Date:</span>
                <span class="text-white">{{ $payment->created_at->format('d F Y H:i') }}</span>
            </div>
            
            
        
        <!-- Payment Proof for Bank Transfer -->
        @if($payment->payment_method == 'transfer')
        <div class="mt-4 pt-4 border-t border-gray-700">
            <h4 class="text-white font-semibold mb-3">
                <i class="fas fa-receipt mr-2 text-yellow-500"></i> Transfer Receipt
            </h4>
            <div class="bg-gray-800 rounded-lg p-4">
                @php
                    // Extract transfer details from note_text
                    $noteText = $payment->note_text ?? '';
                    $bankName = 'N/A';
                    $accountNumber = 'N/A';
                    $transferDate = 'N/A';
                    $proofPath = null;
                    
                    // Parse berdasarkan format
                    $lines = explode("\n", $noteText);
                    foreach ($lines as $line) {
                        if (str_contains($line, 'Bank:')) {
                            $bankName = trim(str_replace('Bank:', '', $line));
                        }
                        if (str_contains($line, 'Account:')) {
                            $accountNumber = trim(str_replace('Account:', '', $line));
                        }
                        if (str_contains($line, 'Transfer Date:')) {
                            $transferDate = trim(str_replace('Transfer Date:', '', $line));
                        }
                        if (str_contains($line, 'Proof:')) {
                            $proofPath = trim(str_replace('Proof:', '', $line));
                        }
                    }
                @endphp
                
                <div class="space-y-2">
                    <div class="flex justify-between border-b border-gray-700 pb-2">
                        <span class="text-gray-400">Bank:</span>
                        <span class="text-white">{{ $bankName }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-700 pb-2">
                        <span class="text-gray-400">Account Number:</span>
                        <span class="text-white font-mono">{{ $accountNumber }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-700 pb-2">
                        <span class="text-gray-400">Transfer Date:</span>
                        <span class="text-white">{{ $transferDate }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-700 pb-2">
                        <span class="text-gray-400">Transfer Amount:</span>
                        <span class="text-green-400 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-700 pb-2">
                        <span class="text-gray-400">Expected Amount:</span>
                        <span class="text-yellow-400 font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($proofPath)
                    <div class="mt-3 pt-3 border-t border-gray-700">
                        <p class="text-gray-400 mb-2">Payment Proof:</p>
                        <a href="{{ asset('storage/' . $proofPath) }}" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-file-image"></i>
                            View Transfer Receipt
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        @else
        <div class="text-center py-8">
            <i class="fas fa-credit-card text-5xl text-gray-600 mb-3"></i>
            <p class="text-gray-400">No payment record found for this booking.</p>
            @if($booking->status == 'pending')
            <a href="{{ route('payment.booking', $booking->id) }}" class="inline-block mt-3 bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600">
                Process Payment
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Cancellation Information (if cancelled or requested) -->
    @if($booking->status == 'cancelled' || $booking->status == 'cancellation_requested')
    <div class="card rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-ban mr-2 text-red-500"></i> Cancellation Information
        </h3>
        <div class="space-y-2">
            @if($booking->cancellation_requested_at)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Requested At:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->cancellation_requested_at)->format('d F Y H:i') }}</span>
            </div>
            @endif
            
            @if($booking->cancellation_reason)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Reason:</span>
                <span class="text-white max-w-md text-right">{{ $booking->cancellation_reason }}</span>
            </div>
            @endif
            
            @if($booking->cancellation_approved_at)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Approved At:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->cancellation_approved_at)->format('d F Y H:i') }}</span>
            </div>
            @endif
            
            @if($booking->cancellation_rejected_at)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Rejected At:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->cancellation_rejected_at)->format('d F Y H:i') }}</span>
            </div>
            @endif
            
            @if($booking->refund_amount > 0)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Refund Amount:</span>
                <span class="text-green-400 font-semibold">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @if($booking->refund_processed_at)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Refund Processed:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->refund_processed_at)->format('d F Y H:i') }}</span>
            </div>
            @endif
            
            @if($booking->cancellation_admin_note)
            <div class="flex justify-between border-b border-gray-700 pb-2">
                <span class="text-gray-400">Admin Note:</span>
                <span class="text-white max-w-md text-right">{{ $booking->cancellation_admin_note }}</span>
            </div>
            @endif
        </div>
        
        @if($booking->status == 'cancellation_requested')
        <div class="mt-4 pt-4 border-t border-gray-700 flex gap-3">
            <button onclick="approveCancellation({{ $booking->id }}, {{ $booking->refund_amount }})" 
                    class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i> Approve Cancellation
            </button>
            <button onclick="rejectCancellation({{ $booking->id }})" 
                    class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-times mr-2"></i> Reject Cancellation
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('admin.bookings.index') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
        
        @if($booking->status == 'pending' || $booking->status == 'confirmed')
        <button onclick="updateBookingStatus({{ $booking->id }})" 
                class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
            <i class="fas fa-edit mr-2"></i> Update Status
        </button>
        @endif
        
        @if($booking->status == 'confirmed')
        <form action="{{ route('admin.bookings.checkin', $booking->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-sign-in-alt mr-2"></i> Check In
            </button>
        </form>
        @endif
        
        @if($booking->status == 'checked_in')
        <form action="{{ route('admin.bookings.checkout', $booking->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Check Out
            </button>
        </form>
        @endif
        
        @if($payment && $payment->payment_status == 'pending' && $payment->payment_method == 'transfer')
        <button onclick="verifyPayment({{ $payment->id }}, {{ $booking->id }})" 
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-check-double mr-2"></i> Verify Payment
        </button>
        @endif
    </div>
</div>

<script>
    function updateBookingStatus(id) {
        Swal.fire({
            title: 'Update Booking Status',
            html: `
                <select id="statusSelect" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            `,
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const newStatus = document.getElementById('statusSelect').value;
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937',
                    color: '#fff'
                });
                
                $.ajax({
                    url: `/admin/bookings/${id}/status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to update status',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    function approveCancellation(id, refundAmount) {
        let formattedAmount = new Intl.NumberFormat('id-ID').format(refundAmount);
        let refundText = refundAmount > 0 
            ? `<div class="bg-green-500/10 rounded-lg p-3 mt-2">
                   <p class="text-green-400">💰 Refund amount: <strong>Rp ${formattedAmount}</strong></p>
               </div>`
            : `<div class="bg-gray-500/10 rounded-lg p-3 mt-2">
                   <p class="text-gray-400">ℹ️ No refund will be issued.</p>
               </div>`;
        
        Swal.fire({
            title: 'Approve Cancellation?',
            html: `
                <div class="text-left">
                    <p>Are you sure you want to approve this cancellation request?</p>
                    ${refundText}
                    <div class="mt-3">
                        <label class="text-gray-300 text-sm block mb-2">Admin Note (optional):</label>
                        <textarea id="adminNote" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm" 
                            placeholder="Add note for customer..."></textarea>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const note = document.getElementById('adminNote')?.value || '';
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937',
                    color: '#fff'
                });
                
                $.ajax({
                    url: `/admin/cancellations/${id}/approve`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_note: note
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Approved!',
                            html: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to approve',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    function rejectCancellation(id) {
        Swal.fire({
            title: 'Reject Cancellation?',
            html: `
                <div class="text-left">
                    <p>Are you sure you want to REJECT this cancellation request?</p>
                    <div class="bg-red-500/10 rounded-lg p-3 mt-2">
                        <p class="text-red-400">⚠️ The booking will remain <strong>CONFIRMED</strong>.</p>
                    </div>
                    <div class="mt-3">
                        <label class="text-gray-300 text-sm block mb-2">Reason for rejection (optional):</label>
                        <textarea id="rejectNote" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm" 
                            placeholder="Tell customer why cancellation was rejected..."></textarea>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const note = document.getElementById('rejectNote')?.value || '';
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#1f2937',
                    color: '#fff'
                });
                
                $.ajax({
                    url: `/admin/cancellations/${id}/reject`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_note: note
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Rejected!',
                            text: response.message,
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to reject',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        });
                    }
                });
            }
        });
    }
    
    function verifyPayment(paymentId, bookingId) {
        Swal.fire({
            title: 'Verify Payment?',
            text: 'Are you sure you want to verify this payment? This will confirm the booking.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Verify',
            cancelButtonText: 'Cancel',
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
                    url: `/admin/payments/${paymentId}/verify`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified!',
                            text: response.message || 'Payment verified successfully. Booking is now confirmed.',
                            confirmButtonColor: '#D4AF37',
                            background: '#1f2937',
                            color: '#fff'
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to verify payment',
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