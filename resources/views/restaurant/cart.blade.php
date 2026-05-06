@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('restaurant.menu') }}" class="text-gray-400 hover:text-yellow-500 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-white">My Cart</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-400">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-400">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        @if(count($cart) > 0)
        <div class="glass-card rounded-2xl p-6">
            <div class="space-y-4">
                @foreach($cart as $id => $item)
                <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-gray-700">
                    <div class="w-16 h-16 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        @if(isset($item['image']) && $item['image'] && file_exists(public_path('storage/' . $item['image'])))
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover rounded-lg">
                        @else
                            <i class="fas fa-utensils text-yellow-500 text-2xl"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-white font-semibold">{{ $item['name'] }}</h3>
                        <p class="text-yellow-500 text-sm">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        <p class="text-gray-500 text-xs">Subtotal: Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $id }}">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="updateQuantity('{{ $id }}', {{ $item['quantity'] - 1 }})" class="w-8 h-8 bg-gray-700 rounded-lg hover:bg-gray-600 transition text-white">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" name="quantity" id="qty-{{ $id }}" value="{{ $item['quantity'] }}" min="1" max="10" 
                                       class="w-14 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1 text-white text-center">
                                <button type="button" onclick="updateQuantity('{{ $id }}', {{ $item['quantity'] + 1 }})" class="w-8 h-8 bg-gray-700 rounded-lg hover:bg-gray-600 transition text-white">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <button type="submit" class="text-blue-500 hover:text-blue-400 ml-2">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </form>
                        <form action="{{ route('cart.remove', $id) }}" method="POST" class="inline" onsubmit="return confirm('Remove {{ $item['name'] }} from cart?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-400 ml-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-700">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Total Items:</span>
                    <span class="text-white font-semibold">{{ $totalQuantity ?? 0 }} item(s)</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Subtotal:</span>
                    <span class="text-white text-xl font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-400">Tax (10%):</span>
                    <span class="text-white">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700">
                    <span class="text-gray-400 font-semibold">Total:</span>
                    <span class="text-yellow-500 text-2xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('restaurant.checkout') }}" method="POST" id="checkoutForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-300 mb-2">Room Number (if staying with us)</label>
                        <input type="text" name="room_number" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" placeholder="e.g., 101">
                        <p class="text-gray-500 text-xs mt-1">Leave empty if not staying at the hotel</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-300 mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" placeholder="Any special requests? (e.g., extra spicy, no onion, etc.)"></textarea>
                    </div>
                    <button type="submit" id="checkoutBtn" class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-3 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle mr-2"></i> Proceed to Checkout
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="glass-card rounded-2xl p-12 text-center">
            <i class="fas fa-shopping-cart text-5xl text-gray-600 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">Your cart is empty</h3>
            <p class="text-gray-400 mb-6">Add some delicious items from our menu</p>
            <a href="{{ route('restaurant.menu') }}" class="inline-block bg-yellow-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition">
                <i class="fas fa-utensils mr-2"></i> Browse Menu
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    input[type="number"]::-webkit-inner-spin-button, 
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 0.5;
    }
</style>

<script>
    function updateQuantity(menuId, newQuantity) {
        if (newQuantity < 1) newQuantity = 1;
        if (newQuantity > 10) newQuantity = 10;
        
        const quantityInput = document.getElementById(`qty-${menuId}`);
        if (quantityInput) {
            quantityInput.value = newQuantity;
            // Find and submit the form
            const form = quantityInput.closest('form');
            if (form) {
                form.submit();
            }
        }
    }
    
    // Checkout confirmation
    document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const roomNumber = document.querySelector('input[name="room_number"]').value;
        const totalItems = {{ $totalQuantity ?? 0 }};
        
        if (totalItems === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Cart is Empty',
                text: 'Please add items to your cart before checkout',
                confirmButtonColor: '#D4AF37',
                background: '#1f2937',
                color: '#fff'
            });
            return;
        }
        
        Swal.fire({
            title: 'Confirm Order',
            html: `
                <div class="text-left">
                    <p>Are you sure you want to place this order?</p>
                    <div class="bg-yellow-500/10 rounded-lg p-3 mt-3">
                        <p class="text-gray-300"><strong>Total Items:</strong> {{ $totalQuantity ?? 0 }}</p>
                        <p class="text-gray-300"><strong>Total Amount:</strong> Rp {{ number_format($total, 0, ',', '.') }}</p>
                        ${roomNumber ? `<p class="text-gray-300"><strong>Room:</strong> ${roomNumber}</p>` : ''}
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#D4AF37',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Place Order',
            cancelButtonText: 'Cancel',
            background: '#1f2937',
            color: '#fff'
        }).then((result) => {
            if (!result.isConfirmed) {
                e.preventDefault();
            } else {
                const btn = document.getElementById('checkoutBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                btn.disabled = true;
            }
        });
    });
</script>
@endsection