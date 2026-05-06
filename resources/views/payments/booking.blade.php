{{-- resources/views/payments/booking.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment - Booking')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-6">Complete Your Payment</h1>
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                <span class="text-green-400">{{ session('success') }}</span>
            </div>
        </div>
        @endif
        
        <!-- Error Message -->
        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                <span class="text-red-400">{{ session('error') }}</span>
            </div>
        </div>
        @endif
        
        <!-- Info Message -->
        @if(session('info'))
        <div class="bg-blue-500/20 border border-blue-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-3 text-xl"></i>
                <span class="text-blue-400">{{ session('info') }}</span>
            </div>
        </div>
        @endif
        
        <div class="glass-card rounded-2xl p-6">
            <!-- Booking Summary -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                    <i class="fas fa-receipt"></i> Booking Summary
                </h3>
                <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Booking ID:</span>
                        <span class="text-white font-semibold">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Room Type:</span>
                        <span class="text-white">{{ $booking->room->roomType->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Room Number:</span>
                        <span class="text-white">#{{ $booking->room->room_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Floor:</span>
                        <span class="text-white">Floor {{ $booking->room->floor ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Check In:</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Check Out:</span>
                        <span class="text-white">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Nights:</span>
                        <span class="text-white">{{ $nights ?? \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} nights</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-700 mt-2">
                        <span class="text-white font-semibold">Total Amount:</span>
                        <span class="text-2xl font-bold text-yellow-500">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Form - Will redirect to respective payment page -->
            <form method="POST" action="{{ route('payment.booking.process', $booking->id) }}" id="paymentForm">
                @csrf
                
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-500 mb-3 flex items-center gap-2">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </h3>
                    <div class="space-y-3">
                        <!-- Cash -->
                        <label class="flex items-center p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition border border-transparent hover:border-yellow-500/30">
                            <input type="radio" name="payment_method" value="cash" class="mr-3 w-4 h-4 text-yellow-500 focus:ring-yellow-500" required>
                            <i class="fas fa-money-bill-wave text-green-500 text-2xl mr-3"></i>
                            <div class="flex-1">
                                <p class="text-white font-semibold">Cash</p>
                                <p class="text-gray-400 text-sm">Pay at the hotel reception (On Site)</p>
                            </div>
                            <i class="fas fa-building text-gray-600 text-xl"></i>
                        </label>
                        
                        <!-- Bank Transfer -->
                        <label class="flex items-center p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition border border-transparent hover:border-yellow-500/30">
                            <input type="radio" name="payment_method" value="transfer" class="mr-3 w-4 h-4 text-yellow-500 focus:ring-yellow-500" required>
                            <i class="fas fa-university text-blue-500 text-2xl mr-3"></i>
                            <div class="flex-1">
                                <p class="text-white font-semibold">Bank Transfer / ATM</p>
                                <p class="text-gray-400 text-sm">Transfer via ATM, Mobile Banking, or Internet Banking</p>
                            </div>
                            <i class="fas fa-arrow-right text-gray-600 text-xl"></i>
                        </label>
                        
                        <!-- Credit Card -->
                        <label class="flex items-center p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition border border-transparent hover:border-yellow-500/30">
                            <input type="radio" name="payment_method" value="credit_card" class="mr-3 w-4 h-4 text-yellow-500 focus:ring-yellow-500" required>
                            <i class="fab fa-cc-visa text-purple-500 text-2xl mr-3"></i>
                            <div class="flex-1">
                                <p class="text-white font-semibold">Credit Card</p>
                                <p class="text-gray-400 text-sm">Visa / Mastercard / JCB / Amex</p>
                            </div>
                            <i class="fas fa-lock text-gray-600 text-xl"></i>
                        </label>
                        
                        <!-- E-Wallet -->
                        <label class="flex items-center p-4 bg-white/5 rounded-lg cursor-pointer hover:bg-white/10 transition border border-transparent hover:border-yellow-500/30">
                            <input type="radio" name="payment_method" value="e_wallet" class="mr-3 w-4 h-4 text-yellow-500 focus:ring-yellow-500" required>
                            <i class="fas fa-mobile-alt text-teal-500 text-2xl mr-3"></i>
                            <div class="flex-1">
                                <p class="text-white font-semibold">E-Wallet</p>
                                <p class="text-gray-400 text-sm">GoPay / OVO / DANA / ShopeePay / LinkAja</p>
                            </div>
                            <i class="fas fa-qrcode text-gray-600 text-xl"></i>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Quick Payment Info -->
                <div id="paymentInfo" class="mb-6 hidden">
                    <div class="bg-gradient-to-r from-yellow-500/10 to-transparent rounded-lg p-4 border border-yellow-500/30">
                        <h4 class="text-yellow-400 font-semibold mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i> Payment Information
                        </h4>
                        <div id="infoContent" class="text-gray-300 text-sm">
                            <!-- Content will be filled dynamically -->
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Notes (Optional)</label>
                    <textarea name="note" rows="3" class="w-full bg-white/10 border border-yellow-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-yellow-500 placeholder:text-gray-500" placeholder="Any special requests? (e.g., extra pillow, late check-in, etc.)"></textarea>
                </div>
                
                @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-0.5"></i>
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
                
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-3 rounded-lg hover:shadow-lg transition transform hover:scale-105 flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i> Continue to Payment
                </button>
                
                <a href="{{ route('my.bookings') }}" class="block w-full text-center text-gray-400 mt-3 hover:text-white transition">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel and Back to Bookings
                </a>
            </form>
        </div>
    </div>
</div>

<script>
    // Payment method info content
    const paymentInfoContent = {
        cash: `
            <div class="space-y-2">
                <p><i class="fas fa-hotel text-yellow-500 mr-2"></i> <strong>Pay at Hotel Reception</strong></p>
                <p>• Go to the hotel front desk</p>
                <p>• Provide your Booking ID: <strong class="text-yellow-500">#{{ $booking->id }}</strong></p>
                <p>• Pay the total amount in cash</p>
                <p>• Staff will confirm your payment and check you in</p>
                <div class="mt-2 pt-2 border-t border-yellow-500/20">
                    <p class="text-yellow-400 text-xs">⚠️ Your booking status will remain <strong>PENDING</strong> until payment is confirmed at the hotel.</p>
                </div>
            </div>
        `,
        transfer: `
            <div class="space-y-2">
                <p><i class="fas fa-university text-blue-500 mr-2"></i> <strong>Bank Transfer Instructions</strong></p>
                <p>You will be redirected to the Bank Transfer payment page with:</p>
                <p>• Complete bank account details (BCA, Mandiri, BRI)</p>
                <p>• Step-by-step transfer instructions</p>
                <p>• Payment proof upload form</p>
                <div class="mt-2 pt-2 border-t border-blue-500/20">
                    <p class="text-blue-400 text-xs">ℹ️ After transfer, upload your payment proof for verification.</p>
                </div>
            </div>
        `,
        credit_card: `
            <div class="space-y-2">
                <p><i class="fab fa-cc-visa text-purple-500 mr-2"></i> <strong>Credit Card Payment</strong></p>
                <p>You will be redirected to the secure Credit Card payment page to enter:</p>
                <p>• Card Number (16 digits)</p>
                <p>• Expiry Date (MM/YY)</p>
                <p>• CVV (3 digits on back of card)</p>
                <p>• Cardholder Name</p>
                <div class="mt-2 pt-2 border-t border-purple-500/20">
                    <p class="text-purple-400 text-xs">🔒 Your payment is secured with 128-bit SSL encryption.</p>
                </div>
            </div>
        `,
        e_wallet: `
            <div class="space-y-2">
                <p><i class="fas fa-mobile-alt text-teal-500 mr-2"></i> <strong>E-Wallet Payment</strong></p>
                <p>You will be redirected to the E-Wallet payment page with options:</p>
                <p>• <i class="fab fa-google-pay"></i> GoPay - Scan QRIS or direct payment</p>
                <p>• <i class="fas fa-wallet"></i> OVO - Quick pay with phone number</p>
                <p>• <i class="fas fa-dharmachakra"></i> DANA - Instant payment</p>
                <p>• ShopeePay - Shopee integrated payment</p>
                <div class="mt-2 pt-2 border-t border-teal-500/20">
                    <p class="text-teal-400 text-xs">📱 No e-wallet? You can also scan the QRIS code using any e-wallet app.</p>
                </div>
            </div>
        `
    };
    
    // Show payment info when method is selected
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const paymentInfo = document.getElementById('paymentInfo');
            const infoContent = document.getElementById('infoContent');
            
            if (this.checked && paymentInfoContent[this.value]) {
                infoContent.innerHTML = paymentInfoContent[this.value];
                paymentInfo.classList.remove('hidden');
                paymentInfo.style.animation = 'fadeIn 0.3s ease-out';
            } else {
                paymentInfo.classList.add('hidden');
            }
        });
    });
    
    // Form submit - redirect to respective payment page
    document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Select Payment Method',
                text: 'Please select a payment method before continuing.',
                confirmButtonColor: '#D4AF37',
                background: '#1f2937',
                color: '#fff'
            });
            return;
        }
        
        // Show confirmation based on method
        let methodName = '';
        let methodIcon = '';
        switch(selectedMethod.value) {
            case 'cash': 
                methodName = 'Cash (Pay at Hotel)';
                methodIcon = '💰';
                break;
            case 'transfer': 
                methodName = 'Bank Transfer / ATM';
                methodIcon = '🏦';
                break;
            case 'credit_card': 
                methodName = 'Credit Card';
                methodIcon = '💳';
                break;
            case 'e_wallet': 
                methodName = 'E-Wallet';
                methodIcon = '📱';
                break;
        }
        
        Swal.fire({
            title: 'Confirm Payment Method',
            html: `${methodIcon} Are you ready to pay with <strong>${methodName}</strong>?<br><br>
                   <div class="bg-yellow-500/10 rounded-lg p-3 mt-2">
                       <p class="text-yellow-400">Amount: <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Continue',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = document.getElementById('submitBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Redirecting to Payment Page...';
                btn.disabled = true;
                e.target.submit();
            } else {
                e.preventDefault();
            }
        });
        
        e.preventDefault();
    });
    
    // Fade in animation style
    if (!document.querySelector('#fadeInStyle')) {
        const style = document.createElement('style');
        style.id = 'fadeInStyle';
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fadeIn 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    }
</script>

<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    input[type="radio"]:checked + div {
        border-color: #D4AF37;
    }
    
    label:has(input:checked) {
        background-color: rgba(212, 175, 55, 0.1);
        border-color: rgba(212, 175, 55, 0.5);
    }
</style>
@endsection