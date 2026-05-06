@extends('layouts.app')

@section('title', 'Credit Card Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fab fa-cc-visa text-4xl text-purple-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Credit Card Payment</h1>
                <p class="text-gray-400 mt-2">Enter your card details</p>
            </div>

            <div class="bg-white/5 rounded-xl p-4 text-center mb-6">
                <p class="text-gray-400">Amount to Pay</p>
                <p class="text-2xl font-bold text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            </div>

            <form action="{{ route('payment.credit-card.process', $booking->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-white mb-2">Card Number</label>
                    <input type="text" name="card_number" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white" placeholder="4111 1111 1111 1111" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-white mb-2">Expiry Date</label>
                        <input type="text" name="expiry_date" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white" placeholder="MM/YY" required>
                    </div>
                    <div>
                        <label class="block text-white mb-2">CVV</label>
                        <input type="text" name="cvv" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white" placeholder="123" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-white mb-2">Cardholder Name</label>
                    <input type="text" name="cardholder_name" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white" placeholder="NAME ON CARD" required>
                </div>

                <div class="mb-6">
                    <label class="block text-white mb-2">Installment</label>
                    <select name="installment" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white">
                        <option value="1">Full Payment</option>
                        <option value="3">3 Months (0% interest)</option>
                        <option value="6">6 Months (0% interest)</option>
                        <option value="12">12 Months (0% interest)</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    <i class="fas fa-lock mr-2"></i> Pay Now
                </button>
            </form>
        </div>
    </div>
</div>
@endsection