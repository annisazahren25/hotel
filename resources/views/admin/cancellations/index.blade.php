@extends('admin.layouts.app')

@section('page_title', 'Cancellation Requests')
@section('page_subtitle', 'Manage booking cancellation requests from guests')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Cancellation Requests</h1>
            <p class="text-gray-400 text-sm mt-1">Review and process cancellation requests from guests</p>
        </div>
        <div class="flex gap-2">
            <span class="bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full text-sm">
                <i class="fas fa-clock mr-1"></i> Pending: {{ $cancellationRequests->total() }}
            </span>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Pending Requests</p>
                    <p class="text-2xl font-bold text-orange-400">{{ $cancellationRequests->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Refund Amount</p>
                    <p class="text-2xl font-bold text-green-400">
                        Rp {{ number_format($cancellationRequests->sum('refund_amount'), 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Avg. Response Time</p>
                    <p class="text-2xl font-bold text-blue-400">24 hours</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Requests Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Total</th>
                        <th>Refund</th>
                        <th>Reason</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellationRequests as $booking)
                    <tr id="cancellation-row-{{ $booking->id }}" class="hover:bg-gray-800/50 transition">
                        <td class="text-gray-400">#{{ $booking->id }}</td>
                        <td>
                            <div>
                                <p class="text-white font-semibold">{{ $booking->guest->name ?? 'N/A' }}</p>
                                <p class="text-gray-500 text-xs">{{ $booking->guest->email ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td>
                            <p class="text-white">{{ $booking->room->room_number ?? 'N/A' }}</p>
                            <p class="text-gray-500 text-xs">{{ $booking->room->roomType->name ?? 'N/A' }}</p>
                        </td>
                        <td class="text-gray-300">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                        <td class="text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="text-green-500">
                            @if($booking->refund_amount > 0)
                                Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td>
                            <p class="text-gray-300 max-w-xs truncate" title="{{ $booking->cancellation_reason }}">
                                {{ Str::limit($booking->cancellation_reason, 40) }}
                            </p>
                        </td>
                        <td class="text-gray-300">
                            {{ $booking->cancellation_requested_at->format('d M Y H:i') }}
                            <p class="text-gray-500 text-xs">{{ $booking->cancellation_requested_at->diffForHumans() }}</p>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="viewDetails({{ $booking->id }})" 
                                        class="text-blue-500 hover:text-blue-400 transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="approveCancellation({{ $booking->id }}, {{ $booking->refund_amount }})" 
                                        class="text-green-500 hover:text-green-400 transition" title="Approve">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <button onclick="rejectCancellation({{ $booking->id }})" 
                                        class="text-red-500 hover:text-red-400 transition" title="Reject">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <i class="fas fa-check-circle text-5xl mb-3 block text-gray-600"></i>
                            <p class="text-lg">No pending cancellation requests</p>
                            <p class="text-sm mt-1">All cancellation requests have been processed</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $cancellationRequests->links() }}
    </div>
</div>

<style>
    .card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid #334155;
    }
    .admin-table tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.1);
    }
</style>

<script>
    function viewDetails(id) {
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching cancellation details',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); },
            background: '#1f2937',
            color: '#fff'
        });
        
        $.ajax({
            url: `/admin/cancellations/${id}/show`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const data = response.data;
                    Swal.fire({
                        title: `Cancellation Request #${data.id}`,
                        html: `
                            <div class="text-left">
                                <div class="bg-gray-800 rounded-lg p-3 mb-3">
                                    <p class="text-gray-400 text-sm">Guest Information</p>
                                    <p class="text-white"><strong>Name:</strong> ${data.guest_name}</p>
                                    <p class="text-white"><strong>Email:</strong> ${data.guest_email}</p>
                                </div>
                                <div class="bg-gray-800 rounded-lg p-3 mb-3">
                                    <p class="text-gray-400 text-sm">Booking Details</p>
                                    <p class="text-white"><strong>Room:</strong> ${data.room_number} (${data.room_type})</p>
                                    <p class="text-white"><strong>Check In:</strong> ${data.check_in_date}</p>
                                    <p class="text-white"><strong>Check Out:</strong> ${data.check_out_date}</p>
                                    <p class="text-white"><strong>Nights:</strong> ${data.nights} nights</p>
                                    <p class="text-yellow-500"><strong>Total Price:</strong> ${data.formatted_price}</p>
                                </div>
                                <div class="bg-gray-800 rounded-lg p-3 mb-3">
                                    <p class="text-gray-400 text-sm">Cancellation Details</p>
                                    <p class="text-white"><strong>Requested:</strong> ${data.cancellation_requested_at}</p>
                                    <p class="text-white"><strong>Reason:</strong> ${data.cancellation_reason || 'No reason provided'}</p>
                                    <p class="text-green-500"><strong>Refund Amount:</strong> ${data.formatted_refund}</p>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonColor: '#D4AF37',
                        confirmButtonText: 'Close',
                        background: '#1f2937',
                        color: '#fff',
                        width: '550px'
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load cancellation details',
                    confirmButtonColor: '#D4AF37',
                    background: '#1f2937',
                    color: '#fff'
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
                   <p class="text-gray-400">ℹ️ No refund will be issued for this booking.</p>
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
</script>
@endsection