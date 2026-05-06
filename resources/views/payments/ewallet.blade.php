@extends('layouts.app')

@section('title', 'E-Wallet Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto">
        <div class="glass-card rounded-2xl p-8">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-teal-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-mobile-alt text-4xl text-teal-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">E-Wallet Payment</h1>
                <p class="text-gray-400 mt-2">Choose your preferred e-wallet</p>
            </div>

            <div class="bg-white/5 rounded-xl p-4 text-center mb-6">
                <p class="text-gray-400">Amount to Pay</p>
                <p class="text-2xl font-bold text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            </div>

            <form action="{{ route('payment.ewallet.process', $booking->id) }}" method="POST">
                @csrf
                
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-4 bg-white/5 rounded-xl cursor-pointer hover:bg-white/10 transition">
                        <input type="radio" name="ewallet_type" value="gopay" class="w-5 h-5 text-teal-500" required>
                        <div class="ml-4 flex-1">
                            <p class="text-white font-semibold">GoPay</p>
                            <p class="text-gray-400 text-sm">Pay with GoPay balance or GoPay Later</p>
                        </div>
                        <i class="fab fa-google-pay text-2xl text-teal-500"></i>
                    </label>

                    <label class="flex items-center p-4 bg-white/5 rounded-xl cursor-pointer hover:bg-white/10 transition">
                        <input type="radio" name="ewallet_type" value="ovo" class="w-5 h-5 text-teal-500">
                        <div class="ml-4 flex-1">
                            <p class="text-white font-semibold">OVO</p>
                            <p class="text-gray-400 text-sm">Pay with OVO points or OVO Cash</p>
                        </div>
                        <i class="fas fa-wallet text-2xl text-teal-500"></i>
                    </label>

                    <label class="flex items-center p-4 bg-white/5 rounded-xl cursor-pointer hover:bg-white/10 transition">
                        <input type="radio" name="ewallet_type" value="dana" class="w-5 h-5 text-teal-500">
                        <div class="ml-4 flex-1">
                            <p class="text-white font-semibold">DANA</p>
                            <p class="text-gray-400 text-sm">Pay with DANA balance</p>
                        </div>
                        <i class="fas fa-dharmachakra text-2xl text-teal-500"></i>
                    </label>

                    <label class="flex items-center p-4 bg-white/5 rounded-xl cursor-pointer hover:bg-white/10 transition">
                        <input type="radio" name="ewallet_type" value="shopeepay" class="w-5 h-5 text-teal-500">
                        <div class="ml-4 flex-1">
                            <p class="text-white font-semibold">ShopeePay</p>
                            <p class="text-gray-400 text-sm">Pay with ShopeePay balance</p>
                        </div>
                        <i class="fas fa-shop text-2xl text-teal-500"></i>
                    </label>
                </div>

                <!-- QRIS -->
                <div class="text-center mb-6">
                    <p class="text-gray-400 mb-3">- OR -</p>
                    <div class="bg-white p-4 rounded-xl inline-block">
                        <div class="w-40 h-40 bg-gray-200 flex items-center justify-center rounded-lg mx-auto">
                            <i class="fas fa-qrcode text-6xl text-gray-500"></i>
                        </div>
                        <p class="text-gray-500 text-sm mt-2">Scan QRIS with any e-wallet</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-teal-500 to-teal-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    <i class="fas fa-arrow-right mr-2"></i> Continue to Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection