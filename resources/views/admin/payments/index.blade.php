@extends('admin.layouts.app')

@section('page_title', 'Payment Management')
@section('page_subtitle', 'Manage all transactions and payments')

@section('content')
<div class="fade-in">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Payment Management</h1>
            <p class="text-gray-400 text-sm mt-1">Manage all hotel transactions and payments</p>
        </div>
        <a href="{{ route('admin.payments.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-download"></i>
            Export Data
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['total'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Today's Revenue</p>
                    <p class="text-2xl font-bold text-green-400">Rp {{ number_format($stats['today'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Pending Payments</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-500"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Completed Payments</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $stats['paid'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-400 text-sm mb-1">Payment Status</label>
                <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" style="color: white; background-color: #1f2937;">
                    <option value="" style="background-color: #1f2937; color: white;">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Paid</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Payment Method</label>
                <select name="method" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" style="color: white; background-color: #1f2937;">
                    <option value="" style="background-color: #1f2937; color: white;">All Methods</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Cash</option>
                    <option value="transfer" {{ request('method') == 'transfer' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Bank Transfer</option>
                    <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">Credit Card</option>
                    <option value="e_wallet" {{ request('method') == 'e_wallet' ? 'selected' : '' }} style="background-color: #1f2937; color: white;">E-Wallet</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" style="color-scheme: dark;">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" style="color-scheme: dark;">
            </div>
            <div class="md:col-span-4">
                <div class="flex gap-2">
                    <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr id="payment-row-{{ $payment->id }}">
                        <td class="text-gray-400">#{{ $payment->id }}</td>
                        <td>
                            @if($payment->booking_id)
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400">
                                    <i class="fas fa-bed mr-1"></i> Booking
                                </span>
                            @elseif($payment->restaurant_order_id)
                                <span class="px-2 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400">
                                    <i class="fas fa-utensils mr-1"></i> Restaurant
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-500/20 text-gray-400">
                                    Other
                                </span>
                            @endif
                        </td>
                        <td class="text-yellow-500 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($payment->payment_method == 'cash') bg-green-500/20 text-green-400
                                @elseif($payment->payment_method == 'transfer') bg-blue-500/20 text-blue-400
                                @elseif($payment->payment_method == 'credit_card') bg-purple-500/20 text-purple-400
                                @else bg-yellow-500/20 text-yellow-400 @endif">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td>
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($payment->payment_status == 'paid') bg-green-500/20 text-green-400
                                @elseif($payment->payment_status == 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-red-500/20 text-red-400 @endif">
                                {{ ucfirst($payment->payment_status) }}
                            </span>
                        </td>
                        <td class="text-gray-300 max-w-xs truncate">{{ $payment->note_text ?? '-' }}</td>
                        <td class="text-gray-300">{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                   class="text-blue-500 hover:text-blue-400 transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($payment->payment_status == 'paid')
                                <button onclick="refundPayment({{ $payment->id }}, {{ $payment->amount }})" 
                                        class="text-yellow-500 hover:text-yellow-400 transition" title="Refund">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-400">
                            <i class="fas fa-credit-card text-4xl mb-2"></i>
                            <p>No payments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</div>

<style>
    /* Fix untuk select options agar terlihat */
    select {
        background-color: #1f2937 !important;
        color: #ffffff !important;
    }
    
    select option {
        background-color: #1f2937 !important;
        color: #ffffff !important;
        padding: 8px !important;
    }
    
    select option:hover {
        background-color: #374151 !important;
    }
    
    select option:checked {
        background-color: #D4AF37 !important;
        color: #000000 !important;
    }
    
    /* Fix untuk date input di dark mode */
    input[type="date"] {
        color-scheme: dark;
        background-color: #1f2937 !important;
        color: white !important;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>

<script>
function refundPayment(id, amount) {
    // Format amount to Rupiah
    let formattedAmount = new Intl.NumberFormat('id-ID').format(amount);
    
    Swal.fire({
        title: 'Process Refund?',
        html: `Are you sure you want to refund <strong class="text-yellow-500">Rp ${formattedAmount}</strong>?<br><br>This action cannot be undone!`,
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
                    let errorMsg = xhr.responseJSON?.message || 'Failed to process refund';
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

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#D4AF37',
        background: '#1f2937',
        color: '#fff',
        timer: 3000
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
</script>
@endsection