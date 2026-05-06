@extends('admin.layouts.app')

@section('page_title', 'Cancellation Requests')
@section('page_subtitle', 'Manage booking cancellation requests')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Cancellation Requests</h1>
            <p class="text-gray-400 text-sm mt-1">Review and process cancellation requests from guests</p>
        </div>
    </div>

    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Total Price</th>
                        <th>Refund Amount</th>
                        <th>Reason</th>
                        <th>Requested At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellationRequests as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->guest->name ?? 'N/A' }}</td>
                        <td>{{ $booking->room->room_number ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</td>
                        <td class="text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="text-green-500">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</td>
                        <td class="max-w-xs truncate" title="{{ $booking->cancellation_reason }}">
                            {{ Str::limit($booking->cancellation_reason, 50) }}
                        </td>
                        <td>{{ $booking->cancellation_requested_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="approveCancellation({{ $booking->id }}, {{ $booking->refund_amount }})" 
                                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button onclick="rejectCancellation({{ $booking->id }})" 
                                        class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-400">
                            <i class="fas fa-check-circle text-4xl mb-2 block"></i>
                            <p>No pending cancellation requests</p>
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

<script>
function approveCancellation(bookingId, refundAmount) {
    let formattedAmount = new Intl.NumberFormat('id-ID').format(refundAmount);
    let refundText = refundAmount > 0 ? `Refund amount: <strong class="text-green-500">Rp ${formattedAmount}</strong>` : 'No refund will be issued.';
    
    Swal.fire({
        title: 'Approve Cancellation?',
        html: `
            Are you sure you want to approve this cancellation?<br><br>
            <div class="bg-yellow-500/10 rounded-lg p-3">
                ${refundText}
            </div>
            <div class="mt-3">
                <label class="text-gray-300 text-sm block mb-2">Admin Note (optional):</label>
                <textarea id="adminNote" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm" 
                    placeholder="Add note for customer..."></textarea>
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
                url: `/admin/cancellations/${bookingId}/approve`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    admin_note: note
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
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

function rejectCancellation(bookingId) {
    Swal.fire({
        title: 'Reject Cancellation?',
        html: `
            Are you sure you want to REJECT this cancellation request?<br><br>
            <div class="bg-red-500/10 rounded-lg p-3">
                The booking will remain CONFIRMED.
            </div>
            <div class="mt-3">
                <label class="text-gray-300 text-sm block mb-2">Reason for rejection (optional):</label>
                <textarea id="rejectNote" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white text-sm" 
                    placeholder="Tell customer why cancellation was rejected..."></textarea>
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
                url: `/admin/cancellations/${bookingId}/reject`,
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