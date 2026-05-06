@extends('layouts.app')

@section('title', 'Book Room')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6">Complete Your Booking</h1>
        
        <div class="glass-card rounded-2xl p-6">
            <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                @csrf
                
                <!-- Hidden fields untuk data booking -->
                <input type="hidden" name="room_id" value="{{ $availableRoom->id ?? '' }}">
                <input type="hidden" name="check_in_date" value="{{ $checkIn ?? '' }}">
                <input type="hidden" name="check_out_date" value="{{ $checkOut ?? '' }}">
                <input type="hidden" name="guests" value="{{ $guests ?? 2 }}">
                
                <!-- Error Messages -->
                @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-3 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-red-400 font-semibold mb-1">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-red-400 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Success Message from Session -->
                @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-green-400">{{ session('success') }}</span>
                    </div>
                </div>
                @endif
                
                <!-- Error Message from Session -->
                @if(session('error'))
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-400">{{ session('error') }}</span>
                    </div>
                </div>
                @endif
                
                <!-- Room Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-bed"></i> Room Details
                    </h3>
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Room Type:</span>
                            <span class="text-white font-semibold">{{ $roomType->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Room Number:</span>
                            <span class="text-white">#{{ $availableRoom->room_number ?? 'To be assigned' }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Floor:</span>
                            <span class="text-white">Floor {{ $availableRoom->floor ?? 'TBA' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Max Guests:</span>
                            <span class="text-white">{{ $roomType->capacity ?? 0 }} persons</span>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i> Booking Details
                    </h3>
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Check In:</span>
                            <span class="text-white">{{ isset($checkIn) ? \Carbon\Carbon::parse($checkIn)->format('l, d F Y') : 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Check Out:</span>
                            <span class="text-white">{{ isset($checkOut) ? \Carbon\Carbon::parse($checkOut)->format('l, d F Y') : 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Number of Nights:</span>
                            <span class="text-white">{{ $nights ?? 0 }} nights</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Number of Guests:</span>
                            <span class="text-white">{{ $guests ?? 0 }} {{ ($guests ?? 0) > 1 ? 'guests' : 'guest' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Price Summary -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-credit-card"></i> Price Summary
                    </h3>
                    <div class="bg-gradient-to-r from-yellow-500/10 to-transparent rounded-lg p-4 border border-yellow-500/20">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Price per night:</span>
                            <span class="text-white">Rp {{ number_format($roomType->price ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-400">Total for {{ $nights ?? 0 }} nights:</span>
                            <span class="text-yellow-500 font-bold">Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-yellow-500/20 mt-2 pt-2 flex justify-between">
                            <span class="text-white font-semibold">Total Payment:</span>
                            <span class="text-xl font-bold text-yellow-500">Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </h3>
                    <p class="text-gray-400 text-sm mb-3">You will be redirected to payment page after confirming booking.</p>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <i class="fas fa-money-bill-wave text-green-500"></i>
                            <span>Cash</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <i class="fas fa-university text-blue-500"></i>
                            <span>Bank Transfer</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <i class="fas fa-credit-card text-purple-500"></i>
                            <span>Credit Card</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <i class="fab fa-digital-ocean text-teal-500"></i>
                            <span>E-Wallet</span>
                        </div>
                    </div>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="terms" name="terms" required class="mr-2 w-4 h-4 rounded border-yellow-500/30 focus:ring-yellow-500">
                        <span class="text-gray-300 text-sm">I agree to the <a href="#" class="text-yellow-500 hover:underline">Terms and Conditions</a> and <a href="#" class="text-yellow-500 hover:underline">Cancellation Policy</a></span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-3 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <i class="fas fa-credit-card mr-2"></i> Continue to Payment
                </button>
                
                <a href="{{ route('rooms.index') }}" class="block w-full text-center text-gray-400 mt-3 hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel and Back to Rooms
                </a>
            </form>
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
    // Form submit with validation
    document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Terms & Conditions',
                text: 'Please agree to the Terms and Conditions to continue.',
                confirmButtonColor: '#D4AF37',
                background: '#1f2937',
                color: '#fff'
            });
            return false;
        }
        
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        btn.disabled = true;
    });
    
    // Sweet Alert for session messages
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