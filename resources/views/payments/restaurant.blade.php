@extends('layouts.app')

@section('title', 'Payment - Restaurant Order')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6">Restaurant Order Payment</h1>
        
        <div class="glass-card rounded-2xl p-6">
            <!-- Order Summary -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3">Order Summary</h3>
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Order ID:</span>
                        <span class="text-white">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Order Type:</span>
                        <span class="text-white">{{ $order->order_type == 'dine_in' ? 'Dine In' : 'Room Delivery' }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Items:</span>
                        <span class="text-white">{{ $order->items->count() }} items</span>
                    </div>
                    @if($order->order_type == 'room_delivery')
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Delivery Address:</span>
                        <span class="text-white">{{ $order->delivery_address ?? 'Hotel Room' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-700 mt-2">
                        <span class="text-white font-semibold">Total Amount:</span>
                        <span class="text-2xl font-bold text-yellow-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Form -->
            <form method="POST" action="{{ route('payment.restaurant.process', $order->id) }}" id="paymentForm">
                @csrf
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3">Payment Method</h3>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                            <input type="radio" name="payment_method" value="cash" class="mr-3 w-4 h-4" required>
                            <i class="fas fa-money-bill-wave text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Cash</p>
                                <p class="text-gray-400 text-sm">Pay at the restaurant</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                            <input type="radio" name="payment_method" value="transfer" class="mr-3 w-4 h-4" required>
                            <i class="fas fa-university text-blue-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Bank Transfer</p>
                                <p class="text-gray-400 text-sm">Transfer to our bank account</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                            <input type="radio" name="payment_method" value="credit_card" class="mr-3 w-4 h-4" required>
                            <i class="fas fa-credit-card text-purple-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">Credit Card</p>
                                <p class="text-gray-400 text-sm">Visa / Mastercard</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition">
                            <input type="radio" name="payment_method" value="e_wallet" class="mr-3 w-4 h-4" required>
                            <i class="fab fa-digital-ocean text-teal-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-white font-semibold">E-Wallet</p>
                                <p class="text-gray-400 text-sm">GoPay / OVO / Dana</p>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea name="note" rows="3" class="w-full bg-white/10 border border-yellow-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500" placeholder="Any special requests?"></textarea>
                </div>
                
                @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-3 mb-4">
                    <ul class="list-disc list-inside text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-3 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <i class="fas fa-credit-card mr-2"></i> Pay Now
                </button>
                
                <a href="{{ route('restaurant.orders') }}" class="block w-full text-center text-gray-400 mt-3 hover:text-white transition">
                    Cancel
                </a>
            </form>
        </div>
        
        <!-- Bank Account Info -->
        <div class="glass-card rounded-2xl p-6 mt-6 hidden" id="bankInfo">
            <h3 class="text-lg font-semibold text-yellow-500 mb-3">Bank Account Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Bank Name:</span>
                    <span class="text-white">Bank Central Asia (BCA)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Account Number:</span>
                    <span class="text-white">123 4567 890</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Account Name:</span>
                    <span class="text-white">PT Val Royale Hotel</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const bankInfo = document.getElementById('bankInfo');
            if (this.value === 'transfer') {
                bankInfo.classList.remove('hidden');
            } else {
                bankInfo.classList.add('hidden');
            }
        });
    });
    
    document.getElementById('paymentForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        btn.disabled = true;
    });
</script>
@endsection