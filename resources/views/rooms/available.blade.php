@extends('layouts.app')

@section('title', 'Available Rooms')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Summary -->
    <div class="glass-card rounded-2xl p-6 mb-8">
        <div class="flex flex-wrap justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2">Search Results</h2>
                <div class="flex flex-wrap gap-4 text-gray-300">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-yellow-500"></i>
                        <span>Check In: <strong class="text-white">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-yellow-500"></i>
                        <span>Check Out: <strong class="text-white">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user text-yellow-500"></i>
                        <span>Guests: <strong class="text-white">{{ $guests }} {{ $guests > 1 ? 'guests' : 'guest' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-moon text-yellow-500"></i>
                        <span>Nights: <strong class="text-white">{{ \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) }} nights</strong></span>
                    </div>
                </div>
            </div>
            <!-- Modify Search - Kembali ke home dengan scroll ke booking form -->
            <a href="{{ route('home') }}#booking" class="mt-4 md:mt-0 text-yellow-500 hover:text-yellow-400 transition">
                <i class="fas fa-edit mr-2"></i> Modify Search
            </a>
        </div>
    </div>

    <!-- Results -->
    @if(count($availableRooms) > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableRooms as $room)
            @php
                // Get the first available room ID
                $firstRoom = $room['rooms'][0] ?? null;
                $roomId = $firstRoom ? $firstRoom->id : '';
            @endphp
            <div class="glass-card rounded-2xl overflow-hidden group hover:transform hover:scale-105 transition-all duration-300">
                @if($room['room_type']->photo_url)
                <img src="{{ $room['room_type']->photo_url }}" alt="{{ $room['room_type']->name }}" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                @else
                <div class="w-full h-56 bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-bed text-4xl text-gray-500"></i>
                </div>
                @endif
                
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-serif text-2xl text-yellow-500">{{ $room['room_type']->name }}</h3>
                        <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full text-xs">
                            {{ $room['available_count'] }} rooms available
                        </span>
                    </div>
                    
                    <p class="text-gray-300 text-sm mb-3">{{ Str::limit($room['room_type']->description ?? 'Luxurious room with premium amenities', 100) }}</p>
                    
                    <div class="flex flex-wrap gap-3 mb-4 text-sm text-gray-400">
                        <span><i class="fas fa-user text-yellow-500 mr-1"></i> Max {{ $room['room_type']->capacity }} guests</span>
                        <span><i class="fas fa-bed text-yellow-500 mr-1"></i> {{ $room['room_type']->capacity == 2 ? 'King Bed' : ($room['room_type']->capacity <= 3 ? 'Queen Bed' : 'Multiple Beds') }}</span>
                    </div>
                    
                    <div class="border-t border-yellow-500/20 pt-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Price per night</span>
                            <span class="text-white">Rp {{ number_format($room['price_per_night'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-400">Total for {{ $room['total_nights'] }} nights</span>
                            <span class="text-yellow-500 font-bold">Rp {{ number_format($room['total_price'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    @auth
                        <a href="{{ route('bookings.create') }}?room_id={{ $roomId }}&room_type_id={{ $room['room_type']->id }}&check_in={{ $checkIn }}&check_out={{ $checkOut }}&guests={{ $guests }}" 
                           class="block w-full text-center bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-2 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                            <i class="fas fa-bookmark mr-2"></i> Book Now
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="block w-full text-center bg-gradient-to-r from-yellow-500 to-yellow-600 text-black font-semibold py-2 rounded-lg hover:shadow-lg transition">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login to Book
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="glass-card rounded-2xl p-12 text-center">
            <i class="fas fa-bed text-6xl text-gray-600 mb-4"></i>
            <i class="fas fa-frown text-6xl text-gray-600 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">No Rooms Available</h3>
            <p class="text-gray-400 mb-6">Sorry, no rooms available for the selected dates and guest count.</p>
            <p class="text-gray-500 text-sm mb-6">Try different dates or fewer guests.</p>
            <a href="{{ route('home') }}#booking" class="inline-block bg-gradient-to-r from-yellow-500 to-yellow-600 text-black px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105">
                <i class="fas fa-search mr-2"></i> Search Again
            </a>
        </div>
    @endif
</div>

<script>
    // Optional: Store last search data
    localStorage.setItem('lastSearch', JSON.stringify({
        check_in: '{{ $checkIn }}',
        check_out: '{{ $checkOut }}',
        guests: '{{ $guests }}'
    }));
</script>
@endsection