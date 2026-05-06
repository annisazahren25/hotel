@extends('layouts.app')

@section('title', 'Restaurant Menu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">🍽️ Restaurant Menu</h1>
                <p class="text-gray-400">Delicious meals prepared by our expert chefs</p>
            </div>
            <a href="{{ route('cart.view') }}" class="relative">
                <div class="bg-yellow-500/20 p-3 rounded-full hover:bg-yellow-500/30 transition">
                    <i class="fas fa-shopping-cart text-yellow-500 text-2xl"></i>
                </div>
                @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $cartCount }}
                </span>
                @endif
            </a>
        </div>

        <!-- Room Booking Info -->
        @if($bookings->count() > 0)
        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 mb-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-hotel text-green-500 text-xl"></i>
                <div>
                    <p class="text-green-400 font-semibold">You are currently staying with us!</p>
                    <p class="text-gray-400 text-sm">You can charge orders to your room. Room: 
                        <strong class="text-white">{{ $bookings->first()->room->room_number ?? 'N/A' }}</strong>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Menu Categories -->
        <div class="flex flex-wrap gap-3 mb-8">
            <button onclick="filterCategory('all')" class="category-filter active bg-yellow-500/20 text-yellow-500 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-500/30 transition" data-category="all">
                All
            </button>
            <button onclick="filterCategory('appetizer')" class="category-filter bg-gray-800/50 text-gray-400 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-500/30 hover:text-yellow-500 transition" data-category="appetizer">
                <i class="fas fa-leaf mr-1"></i> Appetizer
            </button>
            <button onclick="filterCategory('main_course')" class="category-filter bg-gray-800/50 text-gray-400 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-500/30 hover:text-yellow-500 transition" data-category="main_course">
                <i class="fas fa-utensils mr-1"></i> Main Course
            </button>
            <button onclick="filterCategory('dessert')" class="category-filter bg-gray-800/50 text-gray-400 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-500/30 hover:text-yellow-500 transition" data-category="dessert">
                <i class="fas fa-ice-cream mr-1"></i> Dessert
            </button>
            <button onclick="filterCategory('beverage')" class="category-filter bg-gray-800/50 text-gray-400 px-4 py-2 rounded-full text-sm font-semibold hover:bg-yellow-500/30 hover:text-yellow-500 transition" data-category="beverage">
                <i class="fas fa-mug-hot mr-1"></i> Beverage
            </button>
        </div>

        <!-- Menu Items -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($menus as $menu)
            <div class="menu-item glass-card rounded-2xl overflow-hidden hover:transform hover:scale-105 transition-all duration-300" data-category="{{ $menu->category }}">
                <div class="relative h-48 overflow-hidden bg-gray-800">
                    @php
                        // Handle image path - menggunakan photo_url
                        $imageUrl = null;
                        if($menu->photo_url) {
                            // Cek berbagai kemungkinan path
                            if(file_exists(public_path($menu->photo_url))) {
                                $imageUrl = asset($menu->photo_url);
                            } elseif(file_exists(public_path('storage/' . $menu->photo_url))) {
                                $imageUrl = asset('storage/' . $menu->photo_url);
                            } elseif(filter_var($menu->photo_url, FILTER_VALIDATE_URL)) {
                                $imageUrl = $menu->photo_url;
                            }
                        }
                    @endphp
                    
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 flex flex-col items-center justify-center">
                            @if($menu->category == 'appetizer')
                                <i class="fas fa-leaf text-5xl text-yellow-500/50 mb-2"></i>
                            @elseif($menu->category == 'main_course')
                                <i class="fas fa-utensils text-5xl text-yellow-500/50 mb-2"></i>
                            @elseif($menu->category == 'dessert')
                                <i class="fas fa-ice-cream text-5xl text-yellow-500/50 mb-2"></i>
                            @elseif($menu->category == 'beverage')
                                <i class="fas fa-mug-hot text-5xl text-yellow-500/50 mb-2"></i>
                            @else
                                <i class="fas fa-utensils text-5xl text-yellow-500/50 mb-2"></i>
                            @endif
                            <span class="text-gray-500 text-xs">No Image</span>
                        </div>
                    @endif
                    
                    @if($menu->is_available)
                        <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            Available
                        </span>
                    @else
                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            Sold Out
                        </span>
                    @endif
                </div>
                
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-white">{{ $menu->name }}</h3>
                        <p class="text-yellow-500 font-bold text-lg">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-gray-400 text-sm mb-3">{{ $menu->description ?? 'Delicious dish prepared with love' }}</p>
                    
                    @if($menu->is_available)
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <div class="flex gap-2">
                            <input type="number" name="quantity" value="1" min="1" max="10" 
                                   class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-center">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-2 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition flex items-center justify-center gap-2">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                    @else
                    <button disabled class="w-full mt-3 bg-gray-700 text-gray-500 font-semibold py-2 rounded-lg cursor-not-allowed">
                        <i class="fas fa-ban mr-2"></i> Not Available
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-utensils text-5xl text-gray-600 mb-3"></i>
                <p class="text-gray-400">No menu items available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        border-color: rgba(212, 175, 55, 0.3);
    }
    .category-filter.active {
        background-color: rgba(212, 175, 55, 0.2);
        color: #D4AF37;
    }
</style>

<script>
    function filterCategory(category) {
        const items = document.querySelectorAll('.menu-item');
        const buttons = document.querySelectorAll('.category-filter');
        
        buttons.forEach(btn => {
            if (btn.dataset.category === category) {
                btn.classList.add('active');
                btn.classList.remove('bg-gray-800/50', 'text-gray-400');
                btn.classList.add('bg-yellow-500/20', 'text-yellow-500');
            } else {
                btn.classList.remove('active', 'bg-yellow-500/20', 'text-yellow-500');
                btn.classList.add('bg-gray-800/50', 'text-gray-400');
            }
        });
        
        items.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endsection