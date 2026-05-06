@extends('layouts.app')

@section('title', 'Request Cancellation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6">Request Cancellation</h1>
        
        <div class="glass-card rounded-2xl p-6">
            <!-- Warning Box -->
            <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    <span class="text-yellow-500 font-semibold">Cancellation Policy</span>
                </div>
                <p class="text-gray-400 text-sm">Please read the refund policy before proceeding.</p>
            </div>
            
            <!-- Booking Info -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3">Booking Information</h3>
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Booking ID:</span>
                        <span class="text-white">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Room:</span>
                        <span class="text-white">{{ $booking->room->roomType->name }} - #{{ $booking->room->room_number }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Check-in:</span>
                        <span class="text-white">{{ Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Paid:</span>
                        <span class="text-yellow-500 font-bold">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Refund Calculation -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3">Refund Calculation</h3>
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Days before check-in:</span>
                        <span class="text-white">{{ $refundInfo['days_before'] }} days</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Refund Percentage:</span>
                        <span class="text-white">{{ $refundInfo['percentage'] }}%</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-700 mt-2">
                        <span class="text-white font-semibold">Refund Amount:</span>
                        <span class="text-green-400 font-bold">Rp {{ number_format($refundInfo['amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Cancellation Form -->
            <form method="POST" action="{{ route('cancellation.request', $booking->id) }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Reason for Cancellation <span class="text-red-500">*</span></label>
                    <select name="cancellation_reason" id="reasonSelect" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white" required>
                        <option value="">Select a reason</option>
                        <option value="Change of plans">Change of plans</option>
                        <option value="Found better price">Found better price elsewhere</option>
                        <option value="Emergency situation">Emergency situation</option>
                        <option value="Travel restrictions">Travel restrictions</option>
                        <option value="Health issues">Health issues</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4" id="otherReasonDiv" style="display: none;">
                    <label class="block text-gray-300 mb-2">Please specify <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason_detail" rows="3" class="w-full bg-white/10 border border-gray-700 rounded-lg px-4 py-2 text-white" placeholder="Please explain your reason..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="confirm" required class="mr-3 w-4 h-4">
                        <span class="text-gray-300 text-sm">I understand that refund will be processed according to the policy above</span>
                    </label>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition">
                        <i class="fas fa-times mr-2"></i> Submit Cancellation Request
                    </button>
                    <a href="{{ route('my.bookings') }}" class="flex-1 bg-gray-700 text-gray-300 px-4 py-2 rounded-lg font-semibold text-center hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('reasonSelect').addEventListener('change', function() {
        const otherDiv = document.getElementById('otherReasonDiv');
        if (this.value === 'Other') {
            otherDiv.style.display = 'block';
        } else {
            otherDiv.style.display = 'none';
        }
    });
</script>
@endsection